<?php

namespace App\Model;

use App\Base\Message;
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
use App\Structure\FamilyTransformed;
use App\Structure\ModificationTransformed;
use App\Structure\NewContainerTransformed;
use App\Structure\BlockTransformed;
use App\Structure\SequenceTransformed;
use App\Structure\UpdateContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ContainerModel {

    private $usr;
    private $doctrine;
    private $entityManager;
    private $logger;
    private $userRepository;
    private $containerRepository;

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

    function saveSequence(Sequence $modification, SequenceTransformed $trans, Message $message) {

//        $this->entityManager->persist($modification);
//        $this->entityManager->flush();
        return $message;
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

}
