<?php

namespace App\Repository;

use App\Entity\BlockFamily;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method BlockFamily|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlockFamily|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlockFamily[]    findAll()
 * @method BlockFamily[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockFamilyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockFamily::class);
    }

}
