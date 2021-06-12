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

    public function similarity(int $containerId, string $sequenceName) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select fam.id as value, fam.sequence_family_name as label
        from (
	        select seq.id, row_number() over (order by abs(length(:sequenceName) - length(seq.sequence_name)) asc) as RN
	        from msb_sequence seq
	        where lower(substring_index(:sequenceName, \' \', 1)) like concat(\'%\', lower(substring_index(seq.sequence_name, \' \', 1)), \'%\')
		        and seq.container_id = :containerId
        ) seq
	        left join msb_s2f s2f on s2f.sequence_id = seq.id
	        left join msb_sequence_family fam on fam.id = s2f.family_id
        where seq.RN = 1;
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['sequenceName' => $sequenceName, 'containerId' => $containerId]);
        return $stmt->fetchAll();
    }

}
