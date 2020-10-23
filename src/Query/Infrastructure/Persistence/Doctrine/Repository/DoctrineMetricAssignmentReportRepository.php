<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\Program\Participant\MetricAssignmentReportRepository,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Team\TeamProgramParticipation
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineMetricAssignmentReportRepository extends EntityRepository implements MetricAssignmentReportRepository
{

    public function aMetricAssignmentInProgram(string $programId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        $params = [
            "programId" => $programId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];

        $qb = $this->createQueryBuilder("metricAssignmentReport");
        $qb->select("metricAssignmentReport")
                ->andWhere($qb->expr()->eq("metricAssignmentReport.id", ":metricAssignmentReportId"))
                ->leftJoin("metricAssignmentReport.metricAssignment", "metricAssignment")
                ->leftJoin("metricAssignment.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: metric assignment report not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allMetricAssignmentsBelongsToParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "participantId" => $participantId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];

        $qb = $this->createQueryBuilder("metricAssignmentReport");
        $qb->select("metricAssignmentReport")
                ->leftJoin("metricAssignmentReport.metricAssignment", "metricAssignment")
                ->leftJoin("metricAssignment.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aMetricAssignmentReportBelongsToTeam(string $teamId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        $params = [
            "teamId" => $teamId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("metricAssignmentReport");
        $qb->select("metricAssignmentReport")
                ->andWhere($qb->expr()->eq("metricAssignmentReport.id", ":metricAssignmentReportId"))
                ->leftJoin("metricAssignmentReport.metricAssignment", "metricAssignment")
                ->leftJoin("metricAssignment.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: metric assignment report not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allMetricAssignmentReportsInProgramParticipationBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize)
    {
        $params = [
            "teamId" => $teamId,
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(TeamProgramParticipation::class, "teamProgramParticipation")
                ->andWhere($participantQb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.programParticipation", "programParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("metricAssignmentReport");
        $qb->select("metricAssignmentReport")
                ->leftJoin("metricAssignmentReport.metricAssignment", "metricAssignment")
                ->leftJoin("metricAssignment.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
