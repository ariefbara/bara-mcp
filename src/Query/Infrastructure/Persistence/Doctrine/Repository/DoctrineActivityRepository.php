<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ActivityRepository;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Infrastructure\QueryFilter\ActivityFilter;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

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

    public function allActivitiesInProgram(
            string $firmId, string $programId, int $page, int $pageSize, ActivityFilter $activityFilter)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("activity");
        $qb->select("activity")
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        if (!empty($activityFilter->getFrom())) {
            $qb->andWhere($qb->expr()->gte('activity.startEndTime.endDateTime', ':startTime'))
                    ->setParameter('startTime', $activityFilter->getFrom());
        }
        
        if (!empty($activityFilter->getTo())) {
            $qb->andWhere($qb->expr()->lt('activity.startEndTime.endDateTime', ':endTime'))
                    ->setParameter('endTime', $activityFilter->getTo());
        }
        
        $cancelledStatus = $activityFilter->getCancelledStatus();
        if (isset($cancelledStatus)) {
            $qb->andWhere($qb->expr()->eq('activity.cancelled', ':cancelledStatus'))
                    ->setParameter('cancelledStatus', $cancelledStatus);
        }
        
        if (!empty($activityFilter->getActivityTypeIdList())) {
            $qb->andWhere($qb->expr()->in('activityType.id', ':activityTypeIdList'))
                    ->setParameter('activityTypeIdList', $activityFilter->getActivityTypeIdList());
        }
        
        if (!empty($activityFilter->getOrder())) {
            $qb->orderBy('activity.startEndTime.startDateTime', $activityFilter->getOrder());
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
