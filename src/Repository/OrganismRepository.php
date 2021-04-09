<?php

namespace App\Repository;

use App\Entity\Organism;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Organism|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organism|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organism[]    findAll()
 * @method Organism[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganismRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Organism::class);
    }

}
