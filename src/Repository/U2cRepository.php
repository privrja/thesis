<?php

namespace App\Repository;

use App\Entity\U2c;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method U2c|null find($id, $lockMode = null, $lockVersion = null)
 * @method U2c|null findOneBy(array $criteria, array $orderBy = null)
 * @method U2c[]    findAll()
 * @method U2c[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class U2cRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, U2c::class);
    }

    // /**
    //  * @return U2c[] Returns an array of U2c objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?U2c
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
