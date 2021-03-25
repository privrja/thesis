<?php

namespace App\Repository;

use App\Entity\Container;
use App\Structure\Sort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Container|null find($id, $lockMode = null, $lockVersion = null)
 * @method Container|null findOneBy(array $criteria, array $orderBy = null)
 * @method Container[]    findAll()
 * @method Container[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainerRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Container::class);
    }

    /**
     * @param int $containerId
     * @param Sort $sort
     * @return array
     */
    public function getContainerCollaborators(int $containerId, Sort $sort) {
        return $this->createQueryBuilder('cnt')
            ->select('usr.id', 'usr.nick', 'col.mode')
            ->innerJoin('cnt.c2users', 'col')
            ->innerJoin('col.user', 'usr')
            ->where('cnt.id = :containerId')
            ->setParameter('containerId', $containerId)
            ->addOrderBy(($sort->sort === 'mode' ? 'col.' : 'usr.') . $sort->sort, $sort->order)
            ->getQuery()
            ->getArrayResult();
    }

}
