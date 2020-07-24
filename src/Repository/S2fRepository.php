<?php

namespace App\Repository;

use App\Entity\S2f;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method S2f|null find($id, $lockMode = null, $lockVersion = null)
 * @method S2f|null findOneBy(array $criteria, array $orderBy = null)
 * @method S2f[]    findAll()
 * @method S2f[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class S2fRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, S2f::class);
    }

}
