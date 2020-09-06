<?php

namespace App\Repository;

use App\Entity\U2c;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

}
