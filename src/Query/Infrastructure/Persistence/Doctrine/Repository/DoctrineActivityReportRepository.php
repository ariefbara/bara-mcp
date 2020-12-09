<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Program\ActivityReportRepository;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineActivityReportRepository extends EntityRepository implements ActivityReportRepository
{

    public function allActivityReportInActivity(
            string $firmId, string $programId, string $activityId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "activityId" => $activityId,
        ];
        
        $qb = $this->createQueryBuilder("activityReport");
        $qb->select("activityReport")
                ->leftJoin("activityReport.invitee", "invitee")
                ->leftJoin("invitee.activity", "activity")
                ->andWhere($qb->expr()->eq("activity.id", ":activityId"))
                ->leftJoin("activity.activityType", "activityType")
                ->leftJoin("activityType.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function anActivityReportInProgram(string $firmId, string $programId, string $activityReportId): InviteeReport
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "activityReportId" => $activityReportId,
        ];
        
        $qb = $this->createQueryBuilder("activityReport");
        $qb->select("activityReport")
                ->andWhere($qb->expr()->eq("activityReport.id", ":activityReportId"))
                ->leftJoin("activityReport.invitee", "invitee")
                ->leftJoin("invitee.activity", "activity")
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
            $errorDetail = "not found: activity report not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
