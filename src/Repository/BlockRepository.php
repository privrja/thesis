<?php

namespace App\Repository;

use App\Entity\Block;
use App\Structure\Sort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Block|null find($id, $lockMode = null, $lockVersion = null)
 * @method Block|null findOneBy(array $criteria, array $orderBy = null)
 * @method Block[]    findAll()
 * @method Block[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Block::class);
    }

    public function findBlocks(int $containerId, Sort $sort) {
        return $this->createQueryBuilder('blc')
            ->select('blc.id, blc.blockName, blc.acronym, blc.residue as formula, blc.blockMass as mass, blc.blockSmiles as smiles, blc.usmiles as uniqueSmiles, blc.losses, blc.source, blc.identifier, group_concat(fam.blockFamilyName) as family')
            ->leftJoin('blc.b2families', 'b2f')
            ->leftJoin('b2f.family', 'fam', Join::WITH, 'fam.container = blc.container')
            ->where('blc.container = :containerId')
            ->setParameter('containerId', $containerId)
            ->groupBy('blc.id, blc.blockName, blc.acronym, blc.residue, blc.blockMass, blc.blockSmiles, blc.usmiles, blc.losses, blc.source, blc.identifier')
            ->addOrderBy('blc.' . $sort->sort, $sort->order)
            ->getQuery()
            ->getArrayResult();
    }

    function findMergeByFormula(int $containerId) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            select
	            group_concat(distinct src.block_name order by src.block_name separator \'/\') as block_name,
	            group_concat(distinct src.acronym order by src.block_name separator \'/\') as acronym,
                src.residue,
                coalesce(src.block_mass, \'\') as block_mass,
                group_concat(distinct coalesce(src.losses, \'\') order by src.block_name separator \'/\') as losses,
	            group_concat(distinct
		            case when src.source is not null then
			            concat(case
				            when src.source = 0 then \'CID: \'
				            when src.source = 1 then \'CSID: \'
				            when src.source = 2 then \'\'
				            when src.source = 3 then \'PDB: \'
				            else \'SMILES: \'
			            end, case when src.source > 3 then src.usmiles else src.identifier end)
                    else \'\' end
                    order by src.block_name separator \'/\') as ref
            from msb.block src
            where src.container_id = :containerId
            group by src.residue, src.block_mass';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['containerId' => $containerId]);
        return $stmt->fetchAll();
    }

    public function blockUsage(int $containerId, int $blockId, Sort $sort) {
        return $this->createQueryBuilder('blc')
            ->select('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula as formula, seq.sequenceMass as mass, seq.sequenceSmiles as smiles, seq.source, seq.identifier, seq.decays, nmd.modificationName as nModification, cmd.modificationName as cModification, bmd.modificationName as bModification, group_concat(fam.sequenceFamilyName) as family, count(1) as blockUsages')
            ->innerJoin('blc.b2s', 'b2s')
            ->innerJoin('b2s.sequence', 'seq')
            ->leftJoin('seq.s2families', 's2f')
            ->leftJoin('s2f.family', 'fam', Join::WITH, 'fam.container = seq.container')
            ->leftJoin('seq.nModification', 'nmd', Join::WITH, 'nmd.container = seq.container')
            ->leftJoin('seq.cModification', 'cmd', Join::WITH, 'cmd.container = seq.container')
            ->leftJoin('seq.bModification', 'bmd', Join::WITH, 'bmd.container = seq.container')
            ->where('blc.container = :containerId')
            ->andWhere('blc.id = :blockId')
            ->setParameters(['containerId' => $containerId, 'blockId' => $blockId])
            ->groupBy('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula, seq.sequenceMass, seq.sequenceSmiles, seq.source, seq.identifier, seq.decays, nmd.modificationName, cmd.modificationName, bmd.modificationName')
            ->orderBy('seq.' . $sort->sort, $sort->order)
            ->getQuery()
            ->getArrayResult();
    }

    public function findBlockIds(int $containerId, array $smiles) {
        return $this->createQueryBuilder('blc')
            ->select('blc.id')
            ->where('blc.container = :containerId')
            ->andWhere('blc.usmiles in (:usmiles)')
            ->setParameters(['containerId' => $containerId, 'usmiles' => $smiles])
            ->groupBy('blc.id')
            ->getQuery()
            ->getArrayResult();
    }

}
