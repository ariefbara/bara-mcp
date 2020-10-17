<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\PersonnelNotificationRepository,
    Domain\Model\Firm\Personnel\PersonnelNotification
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrinePersonnelNotificationRepository extends EntityRepository implements PersonnelNotificationRepository
{

    public function allNotificationBelongsToPersonnel(string $personnelId, int $page, int $pageSize, ?bool $readStatus)
    {
        $params = [
            "personnelId" => $personnelId,
        ];

        $qb = $this->createQueryBuilder('personnelNotification');
        $qb->select('personnelNotification')
                ->leftJoin('personnelNotification.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($params)
                ->orderBy("personnelNotification.notifiedTime", "DESC");
        
        if (isset($readStatus)) {
            $qb->andWhere($qb->expr()->eq("personnelNotification.read", ":readStatus"))
                    ->setParameter("readStatus", $readStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
