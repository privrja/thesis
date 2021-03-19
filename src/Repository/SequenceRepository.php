<?php

namespace App\Repository;

use App\Base\RepositoryHelper;
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

    public function findSequences($containerId, array $filters, Sort $sort) {
        $qb = $this->createQueryBuilder('seq')
            ->select('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula as formula, seq.sequenceMass as mass, seq.sequenceSmiles as smiles, seq.source, seq.identifier, seq.decays, nmd.modificationName as nModification, cmd.modificationName as cModification, bmd.modificationName as bModification, group_concat(distinct fam.sequenceFamilyName order by fam.sequenceFamilyName asc) as family, group_concat(distinct org.organism order by org.organism asc) as organism')
            ->leftJoin('seq.s2families', 's2f')
            ->leftJoin('seq.s2Organism', 's2o')
            ->leftJoin('s2f.family', 'fam', Join::WITH, 'fam.container = seq.container')
            ->leftJoin('s2o.organism', 'org', Join::WITH, 'org.container = seq.container')
            ->leftJoin('seq.nModification', 'nmd', Join::WITH, 'nmd.container = seq.container')
            ->leftJoin('seq.cModification', 'cmd', Join::WITH, 'cmd.container = seq.container')
            ->leftJoin('seq.bModification', 'bmd', Join::WITH, 'bmd.container = seq.container')
            ->where('seq.container = :containerId')
            ->setParameter('containerId', $containerId);
        $qb = RepositoryHelper::addSequenceFilter($qb, $filters);
        $qb->groupBy('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula, seq.sequenceMass, seq.sequenceSmiles, seq.source, seq.identifier, seq.decays, nmd.modificationName, cmd.modificationName, bmd.modificationName');
        $qb = RepositoryHelper::addHaving($qb, $filters);
        $qb = RepositoryHelper::addSort($qb, $sort);
        return $qb->getQuery()
            ->getArrayResult();
    }

    public function similarity($containerId, $blockIds, $blockLengthUnique, $blockLength) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select fam.id as value, fam.sequence_family_name as label
        from (
        	select
        		seq.id as sequence_id,
        		row_number() over (order by count(distinct b2s.block_id) / (:blockLengthUnique + seq.unique_block_count - count(distinct b2s.block_id)) desc, abs(:blockLength - seq.block_count) asc) as RN
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
            having count(distinct b2s.block_id) / (:blockLengthUnique + seq.unique_block_count - count(distinct b2s.block_id)) >= 0.4
        ) src
        	join msb.s2f on s2f.sequence_id = src.sequence_id
            join msb.sequence_family fam on fam.id = s2f.family_id and fam.container_id = :containerId and fam.sequence_family_name <> \'synthetic\'
        where src.RN = 1
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['blockLengthUnique' => $blockLengthUnique, 'blockLength' => $blockLength, 'containerId' => $containerId]);
        return $stmt->fetchAll();
    }

    public function similarityMore($containerId, $blockIds, $blockLengthUnique, $blockLength) {
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
        		row_number() over (order by count(distinct b2s.block_id) / (:blockLengthUnique + seq.unique_block_count - count(distinct b2s.block_id)) desc, abs(:blockLength - seq.block_count) asc) as RN
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
            having count(distinct b2s.block_id) / (:blockLengthUnique + seq.unique_block_count - count(distinct b2s.block_id)) >= 0.5
        ) src
        where src.RN <= 100
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['blockLengthUnique' => $blockLengthUnique, 'blockLength' => $blockLength, 'containerId' => $containerId]);
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

    public function generateSequence(int $sequenceId) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select src.id, replace(replace(group_concat(concat(case when BRANCH_START = 1 then \'\\\(\' else \'\' end, \'[\', acronym, \']\', case when BRANCH_END = 1 then \'\\\)\' else \'\' end) order by sort asc separator \'-\'), \'-\\\(\', \'\\\(\'), \'\\\)-\', \'\\\)\') as sequence
        from (
	        select seq.id, blc.acronym, b2s.sort,
	            case when seq.sequence_type not in (\'branched\', \'branch-cyclic\') then 0 else row_number() over (order by case when branch_reference_id is not null then 0 else 1 end asc, is_branch asc, sort asc) end as BRANCH_START,
	            case when seq.sequence_type not in (\'branched\', \'branch-cyclic\') then 0 else row_number() over (order by is_branch desc, sort desc) end as BRANCH_END
	        from msb.sequence seq
		        join msb.b2s b2s on b2s.sequence_id = seq.id
		        join msb.block blc on blc.id = b2s.block_id
	        where seq.id = :sequenceId
        ) src
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['sequenceId' => $sequenceId]);
        return $stmt->fetchAll();
    }

}
