<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Program\ActivityRepository,
    Domain\Model\Firm\Program\Activity
};
use Resources\Exception\RegularException;

class DoctrineActivityRepository extends EntityRepository implements ActivityRepository
{

    public function anActivityInProgram(string $firmId, string $programId, string $activityId): Activity
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "activityId" => $activityId,
        ];

        $qb = $this->createQueryBuilder("activity");
        $qb->select("activity")
                ->andWhere($qb->expr()->eq("activity.id", ":activityId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
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

    public function anActivityInFirm(string $firmId, string $activityId): Activity
    {
        $params = [
            "firmId" => $firmId,
            "activityId" => $activityId,
        ];

        $qb = $this->createQueryBuilder("activity");
        $qb->select("activity")
                ->andWhere($qb->expr()->eq("activity.id", ":activityId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->leftJoin("program.firm", "firm")
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
