<?php

namespace App\Repository;

use App\Entity\SequenceFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SequenceFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method SequenceFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method SequenceFamily[]    findAll()
 * @method SequenceFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SequenceFamilyRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, SequenceFamily::class);
    }

}
