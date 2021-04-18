<?php

namespace App\Base;

use App\Structure\Sort;
use Doctrine\ORM\QueryBuilder;

class RepositoryHelper {

    public static function addSequenceFilter(QueryBuilder $qb, array $filters) {
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
        return $qb;
    }


    public static function addSort(QueryBuilder $qb, ?Sort $sort) {
        if (isset($sort)) {
            if ($sort->sort === 'family') {
                $qb->addOrderBy('case when fam.sequenceFamilyName is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('fam.sequenceFamilyName', $sort->order);
            } else if ($sort->sort === 'organism') {
                $qb->addOrderBy('case when org.organism is null then 1 else 0 end', $sort->order)
                    ->addOrderBy('org.organism', $sort->order);
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
        return $qb;
    }

    public static function addHaving(QueryBuilder $qb, array $filters) {
        if (isset($filters['family'])) {
            $qb->andHaving('group_concat(fam.sequenceFamilyName) like concat(\'%\', :family, \'%\')')
                ->setParameter('family', $filters['family']);
        }
        if (isset($filters['organism'])) {
            $qb->andHaving('group_concat(org.organism) like concat(\'%\', :organism, \'%\')')
                ->setParameter('organism', $filters['organism']);
        }
        return $qb;
    }

}
