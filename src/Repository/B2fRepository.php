<?php

namespace App\Repository;

use App\Entity\B2f;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method B2f|null find($id, $lockMode = null, $lockVersion = null)
 * @method B2f|null findOneBy(array $criteria, array $orderBy = null)
 * @method B2f[]    findAll()
 * @method B2f[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class B2fRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, B2f::class);
    }

}
