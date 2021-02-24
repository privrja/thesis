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
use App\Entity\S2f;
use App\Entity\Sequence;
use App\Entity\SequenceFamily;
use App\Entity\U2c;
use App\Entity\User;
use App\Enum\ContainerModeEnum;
use App\Enum\SequenceEnum;
use App\Exception\IllegalStateException;
use App\Smiles\Graph;
use App\Structure\CollaboratorTransformed;
use App\Structure\FamilyTransformed;
use App\Structure\ModificationTransformed;
use App\Structure\NewContainerTransformed;
use App\Structure\BlockTransformed;
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
    private $u2cRepository;
    private $sequenceFamilyRepository;
    private $modificationRepository;

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
        $this->u2cRepository = $doctrine->getRepository(U2c::class);
        $this->sequenceFamilyRepository = $doctrine->getRepository(SequenceFamily::class);
        $this->modificationRepository = $doctrine->getRepository(Modification::class);
    }

    public function concreteContainer(Container $container) {
        $hasContainer = $this->hasContainer($container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
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
        $u2c->setMode(ContainerModeEnum::RWM);
        $this->entityManager->persist($u2c);
        $this->entityManager->flush();
        return Message::createCreated();
    }

    public function update(UpdateContainerTransformed $trans, Container $container): Message {
        $hasContainer = $this->hasContainerRWM($container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
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
        $hasContainer = $this->hasContainerRWM($container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
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

    public function hasContainerRWM(int $containerId) {
        return $this->userRepository->isContainerForLoggedUserByContainerIdRWM($this->usr->getId(), $containerId);
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
        return $this->saveSequence($sequence, $container, $trans, Message::createCreated());
    }

    function setModification($transModification, Container $container) {
        $modification = new Modification();
        $modification->setContainer($container);
        $modification->setModificationName($transModification->modificationName);
        $modification->setModificationFormula($transModification->formula);
        $modification->setModificationMass($transModification->mass);
        $modification->setCTerminal($transModification->cTerminal);
        $modification->setNTerminal($transModification->nTerminal);
        return $modification;
    }

    function setupModification($transModification, Container $container) {
        $bMod = null;
        if (isset($transModification->databaseId)) {
            $bMod = $this->modificationRepository->findOneBy(['container' => $container->getId(), 'id' => $transModification->databaseId]);
            if (!isset($bMod)) {
                return new Message(ErrorConstants::ERROR_MODIFICATION_NOT_FOUND);
            }
        } else if (isset($transModification->modificationName)) {
            $bMod = $this->setModification($transModification, $container);
        }
        return $bMod;
    }

    function saveSequence(Sequence $sequence, Container $container, SequenceTransformed $trans, Message $message) {
        $sequence->setSequenceName($trans->getSequenceName());
        $sequence->setSequenceType($trans->getSequenceType());
        $sequence->setSource($trans->getSource());
        $sequence->setIdentifier($trans->getIdentifier());
        $sequence->setDecays($trans->getDecays());
        $sequence->setSequenceFormula($trans->getFormula());
        $sequence->setSequenceMass($trans->getMass());
        $sequence->setSequenceSmiles($trans->getUsmiles());
        $sequence->setSequence($trans->getSequence());
        $sequence->setSequenceOriginal($trans->getSequenceOriginal());
        switch (SequenceEnum::$backValues[$trans->getSequenceType()]) {
            case SequenceEnum::CYCLIC:
            case SequenceEnum::CYCLIC_POLYKETIDE:
                break;
            default:
            case SequenceEnum::OTHER:
            case SequenceEnum::BRANCHED:
            case SequenceEnum::BRANCH_CYCLIC:
                $modification = $this->setupModification($trans->getBModification(), $container);
                if ($modification instanceof Message) {
                    return $modification;
                }
                $sequence->setBModification($modification);
            /** No break for purpose */
            case SequenceEnum::LINEAR:
            case SequenceEnum::LINEAR_POLYKETIDE:
                $modification = $this->setupModification($trans->getNModification(), $container);
                if ($modification instanceof Message) {
                    return $modification;
                }
                $sequence->setNModification($modification);
                $modification = $this->setupModification($trans->getCModification(), $container);
                if ($modification instanceof Message) {
                    return $modification;
                }
                $sequence->setCModification($modification);
                break;
        }

        foreach ($trans->getFamily() as $family) {
            if (is_numeric($family)) {
                $sFamily = $this->sequenceFamilyRepository->findOneBy(['id' => $family, 'container' => $container->getId()]);
                if (!isset($sFamily)) {
                    return new Message(ErrorConstants::ERROR_SEQUENCE_FAMILY_NOT_FOUND);
                }
            } else {
                $sFamily = new SequenceFamily();
                $sFamily->setContainer($container);
                $sFamily->setSequenceFamilyName($family);
            }
            $s2f = new S2f();
            $s2f->setFamily($sFamily);
            $sequence->addS2family($s2f);
        }

        /** @var Block[] $blockArray */
        $blockArray = [];
        foreach ($trans->getBlocks() as $block) {
            array_push($blockArray, $this->setBlock($block, $container));
        }

        $sequenceHelper = new SequenceHelper($trans->getSequence(), SequenceEnum::$backValues[$trans->getSequenceType()], $blockArray);
        $b2s = $sequenceHelper->sequenceBlocksStructure($trans->getSequenceOriginal());
        foreach ($b2s as $connection) {
            $sequence->addB2($connection);
        }

        $this->entityManager->persist($sequence);
        $this->entityManager->flush();
        return $message;
    }

    private function setBlock($block, $container) {
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
            $sBlock->setContainer($container);
            if (!empty($block->losses)) {
                $sBlock->setLosses($block->losses);
            }
            if (isset($block->source)) {
                $sBlock->setSource($block->source);
            }
            if (!empty($block->identifier)) {
                $sBlock->setIdentifier($block->identifier);
            }
            if ($block->smiles !== null) {
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

    public function deleteSequence(Container $container, Sequence $sequence): Message {
        $hasContainerRW = $this->hasContainerRW($container->getId());
        if (empty($hasContainerRW) || $container->getId() !== $sequence->getContainer()->getId()) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $this->entityManager->remove($sequence);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function createNewCollaborator(User $collaborator, Container $container, CollaboratorTransformed $trans) {
        $hasContainerRWM = $this->hasContainerRWM($container->getId());
        if (empty($hasContainerRWM)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
        }
        $u2c = $this->u2cRepository->findOneBy(['user' => $collaborator->getId(), 'container' => $container->getId()]);
        if ($u2c !== null) {
            return new Message(ErrorConstants::ERROR_USER_ALREADY_IN_CONTAINER);
        }
        $u2c = new U2c();
        $u2c->setContainer($container);
        $u2c->setUser($collaborator);
        $u2c->setMode($trans->getMode());
        $this->entityManager->persist($u2c);
        $this->entityManager->flush();
        return Message::createCreated();
    }

    public function deleteCollaborator(User $collaborator, Container $container): Message {
        $u2c = $this->collaboratorCheck($container, $collaborator);
        if ($u2c instanceof Message) {
            return $u2c;
        }
        $this->entityManager->remove($u2c);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

    public function updateCollaborator(User $collaborator, Container $container, CollaboratorTransformed $trans) {
        $u2c = $this->collaboratorCheck($container, $collaborator);
        if ($u2c instanceof Message) {
            return $u2c;
        }
        $u2c->setMode($trans->getMode());
        $this->entityManager->persist($u2c);
        $this->entityManager->flush();
        return Message::createNoContent();
    }

   private function collaboratorCheck(Container $container, User $collaborator) {
       $hasContainerRWM = $this->hasContainerRWM($container->getId());
       if (empty($hasContainerRWM)) {
           return new Message(ErrorConstants::ERROR_CONTAINER_INSUFIENT_RIGHTS, Response::HTTP_FORBIDDEN);
       }
       $u2c = $this->u2cRepository->findOneBy(['user' => $collaborator->getId(), 'container' => $container->getId()]);
       if ($u2c->getMode() === ContainerModeEnum::RWM) {
           $lastRWM = $this->u2cRepository->count(['container' => $container->getId(), 'mode' => ContainerModeEnum::RWM]);
           if ($lastRWM < 2) {
               return new Message(ErrorConstants::ERROR_CANT_DELETE_LAST_RWM_USER);
           }
       }
       return $u2c;
   }

}
