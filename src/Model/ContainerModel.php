<?php

namespace App\Model;

use App\Base\Message;
use App\Constant\ContainerModeEnum;
use App\Constant\ErrorConstants;
use App\Entity\Container;
use App\Entity\U2c;
use App\Entity\User;
use App\Structure\NewContainerTransformed;
use App\Structure\UpdateContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class ContainerModel
{

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
    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, ?User $usr, LoggerInterface $logger)
    {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->usr = $usr;
        $this->logger = $logger;
        $this->userRepository = $doctrine->getRepository(User::class);
        $this->containerRepository = $doctrine->getRepository(Container::class);
    }

    public function concreteContainer(Container $container) {
        $hasContainer = $this->hasContainer($container);
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER);
        }
        return Message::createOkMessage();
    }

    public function concreteContainerCollaborators($containerId) {
        return $this->containerRepository->getContainerCollaborators($containerId);
    }

    public function createNew(NewContainerTransformed $trans): Message
    {
        $haveSameName = $this->userRepository->isContainerForLoggedUserByName($this->usr->getId(), $trans->getContainerName());
        if (!empty($haveSameName)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NAME_EXISTS);
        }
        $this->createNewContainer($trans);
        return Message::createOkMessage();
    }

    private function createNewContainer(NewContainerTransformed $trans)
    {
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
    }

    public function update(UpdateContainerTransformed $trans, Container $container): Message
    {
        $hasContainer = $this->hasContainer($container);
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER);
        }
        return $this->updateContainerProperties($trans, $container);
    }

    private function updateContainerProperties(UpdateContainerTransformed $trans, Container $container)
    {
        if (!empty($trans->getContainerName())) {
            $container->setContainerName($trans->getContainerName());
        }
        $visibility = $trans->getVisibility();
        if (isset($visibility)) {
            $container->setVisibility($trans->getVisibility());
        }
        $this->entityManager->persist($container);
        $this->entityManager->flush();
        return Message::createOkMessage();
    }

    public function delete(Container $container)
    {
        $hasContainer = $this->hasContainer($container);
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER);
        }
        $this->entityManager->remove($container);
        $this->entityManager->flush();
        return Message::createOkMessage();
    }

    private function hasContainer(Container $container)
    {
        return $this->userRepository->isContainerForLoggedUserByContainerId($this->usr->getId(), $container->getId());
    }

    public function getContainerModifications(int $containerId) {
        return $this->containerRepository->getContainerModifications($containerId);
    }

}
