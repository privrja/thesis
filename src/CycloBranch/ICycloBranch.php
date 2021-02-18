<?php

namespace App\CycloBranch;

use App\Entity\Container;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

interface ICycloBranch {

    /**
     * Import data from file
     * @param Container $container
     * @param EntityManagerInterface $entityManager
     * @param array $okStack
     * @param array $errorStack
     * @return array
     */
    public function import(Container $container, EntityManagerInterface $entityManager, array $okStack, array $errorStack): array ;

    /**
     * Export files in CycloBranch format and download them
     */
    public function export(): Response;

}
