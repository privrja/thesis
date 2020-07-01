<?php

namespace App\Model;

use App\Constant\ContainerModeEnum;
use App\Entity\Container;
use App\Entity\U2c;
use App\Entity\User;
use App\Structure\NewContainerTransformed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ContainerModel {

    private $usr;
    private $doctrine;
    private $entityManager;

    /**
     * ContainerModel constructor.
     * @param EntityManagerInterface $entityManager
     * @param ManagerRegistry $doctrine
     * @param User $usr
     */
    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $doctrine, User $usr) {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
        $this->usr = $usr;
    }

    public function newContainer(NewContainerTransformed $trans) {
        // TODO check for controllers with same name for user?
        $userRepository = $this->doctrine->getRepository(User::class);
//        $userRepository->

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

}
