<?php

namespace App\Model;

use App\Base\FormulaHelper;
use App\Base\Message;
use App\Base\SequenceHelper;
use App\Constant\ErrorConstants;
use App\Entity\Block;
use App\Entity\BlockFamily;
use App\Entity\Container;
use App\Entity\Modification;
use App\Entity\Sequence;
use App\Entity\SequenceFamily;
use App\Entity\U2c;
use App\Entity\User;
use App\Enum\ContainerModeEnum;
use App\Exception\IllegalStateException;
use App\Smiles\Graph;
use App\Structure\FamilyTransformed;
use App\Structure\ModificationTransformed;
use App\Structure\NewContainerTransformed;
use App\Structure\BlockTransformed;
use App\Structure\SequenceEnum;
use App\Structure\SequenceTransformed;
use App\Structure\UpdateContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ContainerModel {

    private $usr;
    private $doctrine;
    private $entityManager;
    private $logger;
    private $userRepository;
    private $containerRepository;
    private $blockRepository;

    /**
     * ContainerModel constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerRegistry $doctrine
     * @param User $usr
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, ?User $usr, LoggerInterface $logger) {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->usr = $usr;
        $this->logger = $logger;
        $this->userRepository = $doctrine->getRepository(User::class);
        $this->containerRepository = $doctrine->getRepository(Container::class);
        $this->blockRepository = $doctrine->getRepository(Block::class);
    }

    public function concreteContainer(Container $container) {
        $hasContainer = $this->hasContainer($container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND);
        }
        return Message::createOkMessage();
    }

    public function concreteContainerCollaborators($containerId) {
        return $this->containerRepository->getContainerCollaborators($containerId);
    }

    public function createNew(NewContainerTransformed $trans): Message {
        $haveSameName = $this->userRepository->isContainerForLoggedUserByName($this->usr->getId(), $trans->getContainerName());
        if (!empty($haveSameName)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NAME_EXISTS);
        }
        return $this->createNewContainer($trans);
    }

    private function createNewContainer(NewContainerTransformed $trans): Message {
        $container = new Container();
        $container->setContainerName($trans->getContainerName());
        $container->setVisibility($trans->getVisibility());
        $this->entityManager->persist($container);

        $u2c = new U2c();
        $u2c->setUser($this->usr);
        $u2c->setContainer($container);
        $u2c->setMode(ContainerModeEnum::RW);
        $this->entityManager->persist($u2c);
        $this->entityManager->flush();
        return Message::createCreated();
    }

    public function update(UpdateContainerTransformed $trans, Container $container): Message {
        $hasContainer = $this->hasContainer($container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND);
        }
        return $this->updateContainerProperties($trans, $container);
    }

    private function updateContainerProperties(UpdateContainerTransformed $trans, Container $container) {
        if (!empty($trans->getContainerName())) {
            $container->setContainerName($trans->getContainerName());
        }
        $visibility = $trans->getVisibility();
        if (isset($visibility)) {
            $container->setVisibility($trans->getVisibility());
        }
        $this->entityManager->persist($container);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function delete(Container $container): Message {
        $hasContainer = $this->hasContainer($container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER, Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($container);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function hasContainer(int $containerId) {
        return $this->userRepository->isContainerForLoggedUserByContainerId($this->usr->getId(), $containerId);
    }

    public function hasContainerRW(int $containerId) {
        return $this->userRepository->isContainerForLoggedUserByContainerIdRW($this->usr->getId(), $containerId);
    }

    public function getContainerModifications(int $containerId) {
        return $this->containerRepository->getContainerModifications($containerId);
    }

    public function deleteBlock(Container $container, Block $block): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $block->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($block);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function updateBlock(BlockTransformed $trans, Container $container, Block $block): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $block->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        return $this->updateBlockProperties($trans, $block);
    }

    private function updateBlockProperties(BlockTransformed $trans, Block $block) {
        return $this->saveBlock($block, $trans, Message::createNoContent());
    }

    public function createNewBlock(Container $container, BlockTransformed $trans): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $block = new Block();
        $block->setContainer($container);
        return $this->saveBlock($block, $trans, Message::createCreated());
    }

    private function saveBlock(Block $block, BlockTransformed $trans, Message $message) {
        $block->setBlockName($trans->getBlockName());
        $block->setAcronym($trans->getAcronym());
        $block->setResidue($trans->getFormula());
        $block->setBlockMass($trans->getMass());
        $block->setLosses($trans->getLosses());
        $block->setBlockSmiles($trans->getSmiles());
        $block->setUsmiles($trans->getUSmiles());
        $block->setSource($trans->getSource());
        $block->setIdentifier($trans->getIdentifier());
        $this->entityManager->persist($block);
        $this->entityManager->flush();
        return $message;
    }

    public function deleteModification(Container $container, Modification $modification): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $modification->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($modification);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function updateModification(ModificationTransformed $trans, Container $container, Modification $modification): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $modification->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        return $this->updateModificationProperties($trans, $modification);
    }

    private function updateModificationProperties(ModificationTransformed $trans, Modification $modification) {
        return $this->saveModification($modification, $trans, Message::createNoContent());
    }

    function saveModification(Modification $modification, ModificationTransformed $trans, Message $message) {
        $modification->setModificationName($trans->getModificationName());
        $modification->setModificationFormula($trans->getFormula());
        $modification->setModificationMass($trans->getMass());
        $modification->setNTerminal($trans->isNTerminal());
        $modification->setCTerminal($trans->isCTerminal());
        $this->entityManager->persist($modification);
        $this->entityManager->flush();
        return $message;
    }

    public function createNewModification(Container $container, ModificationTransformed $trans): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $modification = new Modification();
        $modification->setContainer($container);
        return $this->saveModification($modification, $trans, Message::createCreated());
    }

    public function createNewSequence(Container $container, SequenceTransformed $trans): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $sequence = new Sequence();
        $sequence->setContainer($container);
        return $this->saveSequence($sequence, $trans, Message::createCreated());
    }

    function saveSequence(Sequence $sequence, SequenceTransformed $trans, Message $message) {
        $sequence->setSequenceName($trans->getSequenceName());
        $sequence->setSequenceType($trans->getSequenceType());
        $sequence->setSource($trans->getSource());
        $sequence->setIdentifier($trans->getIdentifier());
        $sequence->setDecays($trans->getDecays());
        $sequence->setSequenceFormula($trans->getFormula());
        $sequence->setSequenceMass($trans->getMass());

        // TODO modifications

        // TODO blocks
        /** @var Block[] $blockArray */
        $blockArray = [];
        foreach ($trans->getBlocks() as $block) {
            array_push($blockArray, $this->setBlock($block));
        }
        $sequenceHelper = new SequenceHelper($trans->getSequence(), SequenceEnum::$backValues[$trans->getSequenceType()], $blockArray);
        $sequenceHelper->sequenceBlocksStructure();

        // TODO save
        return $message;
    }


    private function setBlock($block) {
        if ($block === null) {
            throw new InvalidArgumentException('Block in sequence is not in block array');
        }
        if (isset($block->databaseId)) {
            $sBlock = $this->blockRepository->find($block->databaseId);
            if ($sBlock === null) {
                throw new InvalidArgumentException('Block has defined databaseId, but block in DB never existed');
            }
            return $sBlock;
        } else {
            $sBlock = new Block();
            $sBlock->setBlockName($block->blockName);
            $sBlock->setAcronym($block->acronym);
            if (!empty($block->losses)) {
                $sBlock->setLosses($block->losses);
            }
            if (!empty($block->source)) {
                $sBlock->setSource($block->source);
            }
            if (!empty($block->identifier)) {
                $sBlock->setIdentifier($block->identifier);
            }
            if ($block->smiles === null) {
                $sBlock->setBlockSmiles($block->smiles);
                $graph = new Graph($block->smiles);
                try {
                    $sBlock->setUsmiles($graph->getUniqueSmiles());
                } catch (IllegalStateException $e) {
                    $sBlock->setUsmiles($block->smiles);
                }
                if ($block->formula === null) {
                    $graph->getFormula($block->losses);
                } else {
                    $sBlock->setResidue($block->formula);
                }
                if ($block->mass === null) {
                    try {
                        $sBlock->setBlockMass(FormulaHelper::computeMass($sBlock->getResidue()));
                    } catch (IllegalStateException $e) {
                        /* Empty on purpose, mass can be null */
                    }
                } else {
                    $sBlock->setBlockMass($block->mass);
                }
            } else {
                $sBlock->setResidue($block->formula);
                if ($block->mass === null) {
                    try {
                        $sBlock->setBlockMass(FormulaHelper::computeMass($sBlock->getResidue()));
                    } catch (IllegalStateException $e) {
                        /* Empty on purpose, mass can be null */
                    }
                } else {
                    $sBlock->setBlockMass($block->mass);
                }
            }
            return $sBlock;
        }
    }

    public function createNewBlockFamily(Container $container, FamilyTransformed $trans): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $blockFamily = new BlockFamily();
        $blockFamily->setContainer($container);
        $blockFamily->setBlockFamilyName($trans->getFamily());
        $this->entityManager->persist($blockFamily);
        $this->entityManager->flush();
        return Message::createCreated();
    }

    public function updateBlockFamily(FamilyTransformed $trans, Container $container, BlockFamily $blockFamily): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $blockFamily->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $blockFamily->setBlockFamilyName($trans->getFamily());
        $this->entityManager->persist($blockFamily);
        $this->entityManager->flush();
        return Message::createNoContent();
    }


    public function deleteBlockFamily(Container $container, BlockFamily $blockFamily): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $blockFamily->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($blockFamily);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function createNewSequenceFamily(Container $container, FamilyTransformed $trans): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $blockFamily = new SequenceFamily();
        $blockFamily->setContainer($container);
        $blockFamily->setSequenceFamilyName($trans->getFamily());
        $this->entityManager->persist($blockFamily);
        $this->entityManager->flush();
        return Message::createCreated();
    }

    public function deleteSequenceFamily(Container $container, SequenceFamily $sequenceFamily): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $sequenceFamily->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($sequenceFamily);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function updateSequenceFamily(FamilyTransformed $trans, Container $container, SequenceFamily $sequenceFamily): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $sequenceFamily->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $sequenceFamily->setSequenceFamilyName($trans->getFamily());
        $this->entityManager->persist($sequenceFamily);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

}
