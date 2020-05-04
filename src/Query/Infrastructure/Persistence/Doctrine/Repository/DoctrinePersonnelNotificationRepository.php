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

    public function all(PersonnelCompositionId $personnelCompositionId, int $page, int $pageSize)
    {
        $params = [
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('personnelNotification');
        $qb->select('personnelNotification')
                ->leftJoin('personnelNotification.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(PersonnelCompositionId $personnelCompositionId, string $personnelNotificationId): PersonnelNotification
    {
        $params = [
            "personnelNotificationId" => $personnelNotificationId,
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];

        $qb = $this->createQueryBuilder('personnelNotification');
        $qb->select('personnelNotification')
                ->andWhere($qb->expr()->eq('personnelNotification.id', ':personnelNotificationId'))
                ->leftJoin('personnelNotification.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: personnel notification not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
