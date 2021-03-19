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

    public function findBlocks(int $containerId, array $filters, Sort $sort) {
        $qb = $this->createQueryBuilder('blc')
            ->select('blc.id, blc.blockName, blc.acronym, blc.residue as formula, blc.blockMass as mass, blc.blockSmiles as smiles, blc.usmiles as uniqueSmiles, blc.losses, blc.source, blc.identifier, group_concat(fam.blockFamilyName order by fam.blockFamilyName asc) as family')
            ->leftJoin('blc.b2families', 'b2f')
            ->leftJoin('b2f.family', 'fam', Join::WITH, 'fam.container = blc.container')
            ->where('blc.container = :containerId')
            ->setParameter('containerId', $containerId);
        if (isset($filters['id'])) {
            $qb->andWhere('blc.id = :id')
                ->setParameter('id', $filters['id']);
        }
        if (isset($filters['blockName'])) {
            $qb->andWhere('blc.blockName like concat(\'%\', :blockName, \'%\')')
                ->setParameter('blockName', $filters['blockName']);
        }
        if (isset($filters['acronym'])) {
            $qb->andWhere('blc.acronym like concat(\'%\', :acronym, \'%\')')
                ->setParameter('acronym', $filters['acronym']);
        }
        if (isset($filters['residue'])) {
            $qb->andWhere('blc.residue like concat(\'%\', :residue, \'%\')')
                ->setParameter('residue', $filters['residue']);
        }
        if (isset($filters['blockMassFrom']) && isset($filters['blockMassTo'])) {
            $qb->andWhere('blc.blockMass between :blockMassFrom and :blockMassTo')
                ->setParameter('blockMassFrom', $filters['blockMassFrom'])
                ->setParameter('blockMassTo', $filters['blockMassTo']);
        } else {
            if (isset($filters['blockMassFrom'])) {
                $qb->andWhere('blc.blockMass >= :blockMassFrom')
                    ->setParameter('blockMassFrom', $filters['blockMassFrom']);
            }
            if (isset($filters['blockMassTo'])) {
                $qb->andWhere('blc.blockMass <= :blockMassTo')
                    ->setParameter('blockMassTo', $filters['blockMassTo']);
            }
        }
        if (isset($filters['blockSmiles'])) {
            $qb->andWhere('blc.blockSmiles like concat(\'%\', :blockSmiles, \'%\')')
                ->setParameter('blockSmiles', $filters['blockSmiles']);
        }
        if (isset($filters['losses'])) {
            $qb->andWhere('blc.losses like concat(\'%\', :losses, \'%\')')
                ->setParameter('losses', $filters['losses']);
        }
        if (isset($filters['source'])) {
            $qb->andWhere('blc.source = :source')
                ->setParameter('source', $filters['source']);
        }
        if (isset($filters['identifier'])) {
            $qb->setParameter('identifier', $filters['identifier'])
                ->andWhere('blc.identifier = :identifier');
        }
        $qb->groupBy('blc.id, blc.blockName, blc.acronym, blc.residue, blc.blockMass, blc.blockSmiles, blc.usmiles, blc.losses, blc.source, blc.identifier');

        if (isset($filters['family'])) {
            $qb->having('group_concat(fam.blockFamilyName) like concat(\'%\', :family, \'%\')')
                ->setParameter('family', $filters['family']);
        }

        if ($sort->sort === 'family') {
            $qb->addOrderBy('case when fam.blockFamilyName is null then 1 else 0 end', $sort->order)
                ->addOrderBy('fam.blockFamilyName', $sort->order);
        } else if ($sort->sort === 'identifier') {
            $qb->addOrderBy('blc.source', $sort->order)
                ->addOrderBy('blc.identifier', $sort->order);
        } else {
            $qb->addOrderBy('case when blc.' . $sort->sort . ' is null then 1 else 0 end', $sort->order)
                ->addOrderBy('blc.' . $sort->sort, $sort->order);
        }
        return $qb->getQuery()
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

    public function blockUsage(int $containerId, int $blockId, array $filters, Sort $sort = null) {
        $qb = $this->createQueryBuilder('blc')
            ->select('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula as formula, seq.sequenceMass as mass, seq.sequenceSmiles as smiles, seq.source, seq.identifier, seq.decays, nmd.modificationName as nModification, cmd.modificationName as cModification, bmd.modificationName as bModification, group_concat(distinct fam.sequenceFamilyName order by fam.sequenceFamilyName asc) as family, count(distinct b2s.id) as blockUsages')
            ->innerJoin('blc.b2s', 'b2s')
            ->innerJoin('b2s.sequence', 'seq')
            ->leftJoin('seq.s2families', 's2f')
            ->leftJoin('s2f.family', 'fam', Join::WITH, 'fam.container = seq.container')
            ->leftJoin('seq.nModification', 'nmd', Join::WITH, 'nmd.container = seq.container')
            ->leftJoin('seq.cModification', 'cmd', Join::WITH, 'cmd.container = seq.container')
            ->leftJoin('seq.bModification', 'bmd', Join::WITH, 'bmd.container = seq.container')
            ->where('blc.container = :containerId')
            ->andWhere('blc.id = :blockId')
            ->setParameters(['containerId' => $containerId, 'blockId' => $blockId]);
        if (isset($filters['id'])) {
            $qb->andWhere('seq.id = :id')
                ->setParameter('id', $filters['id']);
        }
        if (isset($filters['sequenceType'])) {
            $qb->andWhere('seq.sequenceType = :sequenceType')
                ->setParameter('sequenceType', $filters['sequenceType']);
        }
        if (isset($filters['sequenceName'])) {
            $qb->andWhere('seq.sequenceName like concat(\'%\', :sequenceName, \'%\')')
                ->setParameter('sequenceName', $filters['sequenceName']);
        }
        if (isset($filters['sequence'])) {
            $qb->andWhere('seq.sequence like concat(\'%\', :sequence, \'%\')')
                ->setParameter('sequence', $filters['sequence']);
        }
        if (isset($filters['sequenceFormula'])) {
            $qb->andWhere('seq.sequenceFormula like concat(\'%\', :sequenceFormula, \'%\')')
                ->setParameter('sequenceFormula', $filters['sequenceFormula']);
        }
        if (isset($filters['sequenceMassFrom']) && isset($filters['sequenceMassTo'])) {
            $qb->andWhere('seq.sequenceMass between :sequenceMassFrom and :sequenceMassTo')
                ->setParameter('sequenceMassFrom', $filters['sequenceMassFrom'])
                ->setParameter('sequenceMassTo', $filters['sequenceMassTo']);
        } else {
            if (isset($filters['sequenceMassFrom'])) {
                $qb->andWhere('seq.sequenceMass >= :sequenceMassFrom')
                    ->setParameter('sequenceMassFrom', $filters['sequenceMassFrom']);
            }
            if (isset($filters['sequenceMassTo'])) {
                $qb->andWhere('seq.sequenceMass <= :sequenceMassTo')
                    ->setParameter('sequenceMassTo', $filters['sequenceMassTo']);
            }
        }
        if (isset($filters['source'])) {
            $qb->andWhere('seq.source = :source')
                ->setParameter('source', $filters['source']);
        }
        if (isset($filters['identifier'])) {
            $qb->setParameter('identifier', $filters['identifier'])
                ->andWhere('seq.identifier = :identifier');
        }
        if (isset($filters['nModification'])) {
            $qb->andWhere('nmd.modificationName like concat(\'%\', :nModification, \'%\')')
                ->setParameter('nModification', $filters['nModification']);
        }
        if (isset($filters['cModification'])) {
            $qb->andWhere('cmd.modificationName like concat(\'%\', :cModification, \'%\')')
                ->setParameter('cModification', $filters['cModification']);
        }
        if (isset($filters['bModification'])) {
            $qb->andWhere('bmd.modificationName like concat(\'%\', :bModification, \'%\')')
                ->setParameter('bModification', $filters['bModification']);
        }
        $qb->groupBy('seq.id, seq.sequenceType, seq.sequenceName, seq.sequence, seq.sequenceFormula, seq.sequenceMass, seq.sequenceSmiles, seq.source, seq.identifier, seq.decays, nmd.modificationName, cmd.modificationName, bmd.modificationName');
        if (isset($filters['family']) && !isset($filters['usages'])) {
            $qb->having('group_concat(fam.sequenceFamilyName) like concat(\'%\', :family, \'%\')')
                ->setParameter('family', $filters['family']);
        } else if (isset($filters['usages']) && !isset($filters['family'])) {
            $qb->having('count(1) = :usages')
                ->setParameter('usages', $filters['usages']);
        } else if (isset($filters['family']) && isset($filters['usages'])) {
            $qb->having('group_concat(fam.sequenceFamilyName) like concat(\'%\', :family, \'%\') and count(1) = :usages')
                ->setParameter('family', $filters['family'])
                ->setParameter('usages', $filters['usages']);
        }
        if (isset($sort)) {
            if ($sort->sort === 'family') {
                $qb->addOrderBy('case when fam.sequenceFamilyName is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('fam.sequenceFamilyName', $sort->order);
            } else if ($sort->sort === 'nModification') {
                $qb->addOrderBy('case when nmd.modificationName is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('nmd.modificationName', $sort->order);
            } else if ($sort->sort === 'cModification') {
                $qb->addOrderBy('case when cmd.modificationName is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('cmd.modificationName', $sort->order);
            } else if ($sort->sort === 'bModification') {
                $qb->addOrderBy('case when bmd.modificationName is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('bmd.modificationName', $sort->order);
            } else if ($sort->sort === 'identifier') {
                $qb->addOrderBy('seq.source', $sort->order)
                    ->addOrderBy('seq.identifier', $sort->order);
            } else if ($sort->sort === 'usages') {
                $qb->addOrderBy('blockUsages', $sort->order);
            } else {
                $qb->addOrderBy('case when seq.' . $sort->sort . ' is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('seq.' . $sort->sort, $sort->order);
            }
        }
        return $qb->getQuery()
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
