<?php

namespace App\Model;

use App\Entity\Container;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class BlockModel {

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




}
