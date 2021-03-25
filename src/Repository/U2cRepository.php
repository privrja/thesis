<?php

namespace App\Repository;

use App\Entity\U2c;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method U2c|null find($id, $lockMode = null, $lockVersion = null)
 * @method U2c|null findOneBy(array $criteria, array $orderBy = null)
 * @method U2c[]    findAll()
 * @method U2c[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class U2cRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, U2c::class);
    }

    public function userDeleteContainers(int $userId) {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
        select rol.container_id,
	        case
		        when sum(case when rol.mode = \'RW\' then 1 else 0 end) > 0 then \'UPGRADE\'
                when sum(case when rol.mode in (\'R\', \'RW\') then 1 else 0 end) > 0 then \'ADMIN\'
                else \'DELETE\'
            end as operation_todo
        from msb_u2c rol
	        join (
		        select rol.container_id, count(1) as COUNT_RWM
		        from msb_u2c rol
			        join (
				        select rol.container_id
				        from msb_u2c rol
					        join msb_user usr on usr.id = rol.user_id and usr.nick <> \'admin\'
				        where rol.mode = \'RWM\' and rol.user_id = :userId
			        ) src on src.container_id = rol.container_id and rol.mode = \'RWM\'
		        group by rol.container_id
	        ) src on src.container_id = rol.container_id and src.COUNT_RWM < 2
        group by rol.container_id
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

}
