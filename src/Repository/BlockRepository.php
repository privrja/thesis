<?php

namespace App\Repository;

use App\Entity\Block;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    function findMergeByFormula(int $containerId) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            select
	            group_concat(distinct src.block_name order by src.block_name separator \'/\') as block_name,
	            group_concat(distinct src.acronym order by src.block_name separator \'/\') as acronym,
                group_concat(distinct coalesce(src.block_mass, \'\') order by src.block_name separator \'/\') as block_mass,
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
        $stmt->execute(array('containerId' => $containerId));
        return $stmt->fetch();
    }


}
