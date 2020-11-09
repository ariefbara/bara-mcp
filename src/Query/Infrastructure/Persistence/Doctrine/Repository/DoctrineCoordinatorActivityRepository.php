<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramCoordinator\CoordinatorActivityRepository,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineCoordinatorActivityRepository extends EntityRepository implements CoordinatorActivityRepository
{

    public function allActivitiesBelongsToCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "coordinatorId" => $coordinatorId,
        ];
        
        $qb = $this->createQueryBuilder("coordinatorActivity");
        $qb->select("coordinatorActivity")
                ->leftJoin("coordinatorActivity.coordinator", "coordinator")
                ->andWhere($qb->expr()->eq("coordinator.id", ":coordinatorId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityBelongsToCoordinator(string $firmId, string $personnelId, string $activityId): CoordinatorActivity
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "activityId" => $activityId,
        ];
        
        $qb = $this->createQueryBuilder("coordinatorActivity");
        $qb->select("coordinatorActivity")
                ->andWhere($qb->expr()->eq("coordinatorActivity.id", ":activityId"))
                ->leftJoin("coordinatorActivity.coordinator", "coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
