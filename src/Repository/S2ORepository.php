<?php

namespace App\Repository;

use App\Entity\S2O;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method S2O|null find($id, $lockMode = null, $lockVersion = null)
 * @method S2O|null findOneBy(array $criteria, array $orderBy = null)
 * @method S2O[]    findAll()
 * @method S2O[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class S2ORepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, S2O::class);
    }

    // /**
    //  * @return S2O[] Returns an array of S2O objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?S2O
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
