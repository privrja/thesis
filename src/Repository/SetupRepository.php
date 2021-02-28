<?php

namespace App\Repository;

use App\Entity\Setup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Setup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setup[]    findAll()
 * @method Setup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SetupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setup::class);
    }

    // /**
    //  * @return Setup[] Returns an array of Setup objects
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
    public function findOneBySomeField($value): ?Setup
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
