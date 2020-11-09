<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Manager\ManagerActivityRepository,
    Domain\Model\Firm\Manager\ManagerActivity
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineManagerActivityRepository extends EntityRepository implements ManagerActivityRepository
{
    
    public function allActivitiesBelongsToManager(string $firmId, string $managerId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];
        
        $qb = $this->createQueryBuilder("managerActivity");
        $qb->select("managerActivity")
                ->leftJoin("managerActivity.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityBelongsToManager(string $firmId, string $managerId, string $activityId): ManagerActivity
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "activityId" => $activityId,
        ];
        
        $qb = $this->createQueryBuilder("managerActivity");
        $qb->select("managerActivity")
                ->andWhere($qb->expr()->eq("managerActivity.id", ":activityId"))
                ->leftJoin("managerActivity.manager", "manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", "firm")
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
