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

    public function similarity($containerId, $blockIds, $blockLength) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select fam.id as value, fam.sequence_family_name as label
        from (
        	select
        		seq.id as sequence_id,
        		row_number() over (order by count(distinct b2s.block_id) / (:blockLength + seq.unique_block_count - count(distinct b2s.block_id)) desc) as RN
        	from sequence seq
        		left join msb.b2s b2s on b2s.sequence_id = seq.id and b2s.block_id in ' . $blockIds . '
        		join (
        			select seq.id
        			from msb.sequence seq
        				join msb.s2f on s2f.sequence_id = seq.id and s2f.family_id is not null
        			group by seq.id
                ) fam on fam.id = seq.id
            where seq.container_id = :containerId
        	group by seq.sequence_name, seq.id, seq.unique_block_count
            having count(distinct b2s.block_id) / (:blockLength + seq.unique_block_count - count(distinct b2s.block_id)) >= 0.4
        ) src
        	join msb.s2f on s2f.sequence_id = src.sequence_id
            join msb.sequence_family fam on fam.id = s2f.family_id and fam.container_id = :containerId
        where src.RN = 1
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['blockLength' => $blockLength, 'containerId' => $containerId]);
        return $stmt->fetchAll();
    }

    public function similarityMore($containerId, $blockIds, $blockLength) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select src.id, src.sequenceName, src.smiles, src.formula, src.mass
        from (
        	select
        		seq.id,
        	    seq.sequence_name as sequenceName,
                seq.sequence_smiles as smiles,
        	    seq.sequence_formula as formula,
        	    seq.sequence_mass as mass,
        		row_number() over (order by count(distinct b2s.block_id) / (:blockLength + seq.unique_block_count - count(distinct b2s.block_id)) desc) as RN
        	from sequence seq
        		left join msb.b2s b2s on b2s.sequence_id = seq.id and b2s.block_id in ' . $blockIds . '
        		join (
        			select seq.id
        			from msb.sequence seq
        				join msb.s2f on s2f.sequence_id = seq.id and s2f.family_id is not null
        			group by seq.id
                ) fam on fam.id = seq.id
            where seq.container_id = :containerId
        	group by seq.unique_block_count, seq.id, seq.sequence_name, seq.sequence_smiles, seq.sequence_formula, seq.sequence_mass
            having count(distinct b2s.block_id) / (:blockLength + seq.unique_block_count - count(distinct b2s.block_id)) >= 0.5
        ) src
        where src.RN <= 100
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['blockLength' => $blockLength, 'containerId' => $containerId]);
        return $stmt->fetchAll();
    }

    public function name(int $containerId, string $name) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select seq.sequence_name as sequenceName, seq.sequence_smiles as smiles, seq.sequence_formula as formula, seq.sequence_mass as mass, seq.id
        from msb.sequence seq
        where seq.sequence_name like concat(\'%\', :sequenceName, \'%\') and seq.container_id = :containerId
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['containerId' => $containerId, 'sequenceName' => $name]);
        return $stmt->fetchAll();
    }

}
