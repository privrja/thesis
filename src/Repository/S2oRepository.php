<?php

namespace App\Repository;

use App\Entity\S2o;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method S2o|null find($id, $lockMode = null, $lockVersion = null)
 * @method S2o|null findOneBy(array $criteria, array $orderBy = null)
 * @method S2o[]    findAll()
 * @method S2o[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class S2oRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, S2o::class);
    }

}
