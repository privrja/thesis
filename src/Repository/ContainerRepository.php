<?php

namespace App\Repository;

use App\Entity\Container;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Container|null find($id, $lockMode = null, $lockVersion = null)
 * @method Container|null findOneBy(array $criteria, array $orderBy = null)
 * @method Container[]    findAll()
 * @method Container[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContainerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Container::class);
    }

}
