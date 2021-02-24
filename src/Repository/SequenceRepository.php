<?php

namespace App\Repository;

use App\Entity\Sequence;
use App\Structure\Sort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sequence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sequence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sequence[]    findAll()
 * @method Sequence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SequenceRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Sequence::class);
    }

    public function findSequences($containerId, Sort $sort) {
        return $this->createQueryBuilder('seq')
            ->select('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula as formula, seq.sequenceMass as mass, seq.sequenceSmiles as smiles, seq.source, seq.identifier, seq.decays, nmd.modificationName as nModification, cmd.modificationName as cModification, bmd.modificationName as bModification, group_concat(fam.sequenceFamilyName) as family')
            ->leftJoin('seq.s2families', 's2f')
            ->leftJoin('s2f.family', 'fam', Join::WITH, 'fam.container = seq.container')
            ->leftJoin('seq.nModification', 'nmd', Join::WITH, 'nmd.container = seq.container')
            ->leftJoin('seq.cModification', 'cmd', Join::WITH, 'cmd.container = seq.container')
            ->leftJoin('seq.bModification', 'bmd', Join::WITH, 'bmd.container = seq.container')
            ->where('seq.container = :containerId')
            ->setParameter('containerId', $containerId)
            ->groupBy('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula, seq.sequenceMass, seq.sequenceSmiles, seq.source, seq.identifier, seq.decays, nmd.modificationName, cmd.modificationName, bmd.modificationName')
            ->orderBy('seq.' . $sort->sort, $sort->order)
            ->getQuery()
            ->getArrayResult();
    }

}
