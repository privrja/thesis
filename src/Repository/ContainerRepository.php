<?php

namespace App\Repository;

use App\Entity\Container;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Container|null find($id, $lockMode = null, $lockVersion = null)
 * @method Container|null findOneBy(array $criteria, array $orderBy = null)
 * @method Container[]    findAll()
 * @method Container[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Container::class);
    }

    /**
     * @param int $containerId
     * @return array
     */
    public function getContainerCollaborators(int $containerId) {
        return $this->createQueryBuilder('cnt')
            ->select('usr.id', 'usr.nick', 'col.mode')
            ->innerJoin('cnt.c2users', 'col')
            ->innerJoin('col.user', 'usr')
            ->where('cnt.id = :containerId')
            ->setParameter('containerId', $containerId)
            ->getQuery()
            ->getArrayResult();
    }

    public function getContainerModifications(int $containerId) {
        return $this->createQueryBuilder('cnt')
            ->select('mdf.id', 'cnt.id as containerId', 'mdf.modificationName', 'mdf.modificationFormula', 'mdf.modificationMass', 'mdf.nTerminal', 'mdf.cTerminal')
            ->innerJoin('cnt.modificationId', 'mdf')
            ->where('cnt.id = :containerId')
            ->setParameter('containerId', $containerId)
            ->getQuery()
            ->getArrayResult();
    }


}
