<?php

namespace App\Repository;

use App\Entity\Container;
use App\Entity\Modification;
use App\Structure\Sort;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Modification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modification[]    findAll()
 * @method Modification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModificationRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Modification::class);
    }

    public function filters(Container $container, array $filters, Sort $sort) {
        $qb = $this->createQueryBuilder('mdf')
            ->andWhere('mdf.container = :container')
            ->setParameter('container', $container);
        if (isset($filters['id'])) {
            $qb->andWhere('mdf.id = :id')
                ->setParameter('id', $filters['id']);
        }
        if (isset($filters['modificationName'])) {
            $qb->andWhere('mdf.modificationName like concat(\'%\', :modificationName, \'%\')')
                ->setParameter('modificationName', $filters['modificationName']);
        }
        if (isset($filters['modificationFormula'])) {
            $qb->andWhere('mdf.modificationFormula like concat(\'%\', :modificationFormula, \'%\')')
                ->setParameter('modificationFormula', $filters['modificationFormula']);
        }
        if (isset($filters['modificationMassFrom']) && isset($filters['modificationMassTo'])) {
            $qb->andWhere('mdf.modificationMass between :modificationMassFrom and :modificationMassTo')
                ->setParameter('modificationMassFrom', $filters['modificationMassFrom'])
                ->setParameter('modificationMassTo', $filters['modificationMassTo']);
        } else {
            if (isset($filters['modificationMassFrom'])) {
                $qb->andWhere('mdf.modificationMass >= :modificationMassFrom')
                    ->setParameter('modificationMassFrom', $filters['modificationMassFrom']);
            }
            if (isset($filters['modificationMassTo'])) {
                $qb->andWhere('mdf.modificationMass <= :modificationMassTo')
                    ->setParameter('modificationMassTo', $filters['modificationMassTo']);
            }
        }
        if (isset($filters['nTerminal'])) {
            $qb->andWhere('mdf.nTerminal = :nTerminal')
                ->setParameter('nTerminal', $filters['nTerminal']);
        }
        if (isset($filters['cTerminal'])) {
            $qb->andWhere('mdf.cTerminal = :cTerminal')
                ->setParameter('cTerminal', $filters['cTerminal']);
        }
        if ($sort->sort === 'id') {
            $qb->addOrderBy('mdf.id', $sort->order);
        } else if ($sort->sort === 'modificationName') {
            $qb->addOrderBy('mdf.modificationName', $sort->order);
        } else if ($sort->sort === 'modificationFormula') {
            $qb->addOrderBy('mdf.modificationFormula', $sort->order);
        } else if ($sort->sort === 'modificationMass') {
            $qb->addOrderBy('case when mdf.modificationMass is null then 1 else 0 end', $sort->order)
            ->addOrderBy('mdf.modificationMass', $sort->order);
        } else if ($sort->sort === 'nTerminal') {
            $qb->addOrderBy('mdf.nTerminal', $sort->order);
        } else if ($sort->sort === 'cTerminal') {
            $qb->addOrderBy('mdf.cTerminal', $sort->order);
        }
        return $qb->getQuery()->getArrayResult();
    }

}
