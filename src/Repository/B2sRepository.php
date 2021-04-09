<?php

namespace App\Repository;

use App\Entity\B2s;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method B2s|null find($id, $lockMode = null, $lockVersion = null)
 * @method B2s|null findOneBy(array $criteria, array $orderBy = null)
 * @method B2s[]    findAll()
 * @method B2s[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class B2sRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, B2s::class);
    }

}
