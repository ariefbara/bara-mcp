<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Application\Service\Firm\Personnel\CoordinatorNotificationRepository;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineCoordinatorNotificationRepository extends EntityRepository implements CoordinatorNotificationRepository
{

    public function allNotificationForCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorId" => $coordinatorId,
        ];
        
        $qb = $this->createQueryBuilder("coordinatorNotification");
        $qb->select("coordinatorNotification")
                ->andWhere($qb->expr()->eq("coordinatorNotification.read", "false"))
                ->leftJoin("coordinatorNotification.coordinator", "coordinator")
                ->andWhere($qb->expr()->eq("coordinator.id", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->orderBy("coordinatorNotification.notifiedTime", "DESC");
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
