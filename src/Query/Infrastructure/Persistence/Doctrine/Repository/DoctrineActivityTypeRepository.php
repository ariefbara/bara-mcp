<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\ActivityTypeRepository,
    Domain\Model\Firm\Program\ActivityType
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineActivityTypeRepository extends EntityRepository implements ActivityTypeRepository
{

    public function allActivityTypesInProgram(string $programId, int $page, int $pageSize)
    {
        $params = [
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("activityType");
        $qb->select("activityType")
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function anActivityTypeInProgram(string $programId, string $activityTypeId): ActivityType
    {
        $params = [
            "programId" => $programId,
            "activityTypeId" => $activityTypeId,
        ];
        
        $qb = $this->createQueryBuilder("activityType");
        $qb->select("activityType")
                ->andWhere($qb->expr()->eq("activityType.id", ":activityTypeId"))
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: activity type not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
