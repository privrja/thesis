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
            from msb_sequence_family fam
            where :sequenceName like concat(\'%\', fam.sequence_family_name, \'%\')
	            and container_id = :containerId
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['sequenceName' => $sequenceName, 'containerId' => $containerId]);
        return $stmt->fetchAll();
    }

}
