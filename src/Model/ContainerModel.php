<?php

namespace App\Model;

use App\Base\Message;
use App\Constant\ContainerModeEnum;
use App\Constant\ErrorConstants;
use App\Controller\UpdateContainerTransformed;
use App\Entity\Container;
use App\Entity\U2c;
use App\Entity\User;
use App\Structure\NewContainerTransformed;
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

    /**
     * ContainerModel constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerRegistry $doctrine
     * @param User $usr
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, User $usr, LoggerInterface $logger) {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->usr = $usr;
        $this->logger = $logger;
        $this->userRepository = $doctrine->getRepository(User::class);
    }

    public function createNew(NewContainerTransformed $trans): Message {
        $haveSameName = $this->userRepository->isContainerForLoggedUserByName($this->usr->getId(), $trans->getName());
        if (!empty($haveSameName)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NAME_EXISTS);
        }
        $this->createNewContainer($trans);
        return Message::createOkMessage();
    }

    private function createNewContainer(NewContainerTransformed $trans) {
        $container = new Container();
        $container->setName($trans->getName());
        $container->setVisibility($trans->getVisibility());
        $this->entityManager->persist($container);

        $u2c = new U2c();
        $u2c->setUser($this->usr);
        $u2c->setContainer($container);
        $u2c->setMode(ContainerModeEnum::RW);
        $this->entityManager->persist($u2c);
        $this->entityManager->flush();
    }

    public function update(UpdateContainerTransformed $trans, Container $container): Message {
        $hasContainer = $this->userRepository->isContainerForLoggedUserByContainerId($this->usr->getId(), $container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER);
        }
        $this->updateContainerProperties($trans, $container);
        return Message::createOkMessage();
    }

    private function updateContainerProperties(UpdateContainerTransformed $trans, Container $container) {
        if (!empty($trans->getName())) {
            $container->setName($trans->getName());
        }

        $visibility = $trans->getVisibility();
        if (isset($visibility)) {
            $container->setVisibility($trans->getVisibility());
        }

        $this->entityManager->flush();
    }

    public function delete(Container $container) {
        $hasContainer = $this->userRepository->isContainerForLoggedUserByContainerId($this->usr->getId(), $container->getId());
        if (empty($hasContainer)) {
            return new Message(ErrorConstants::ERROR_CONTAINER_NOT_EXISTS_FOR_USER);
        }
        $this->entityManager->remove($container);
        $this->entityManager->flush();
        return Message::createOkMessage();
    }

}
