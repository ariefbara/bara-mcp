<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Program\Participant\MetricAssignmentReportRepository,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};
use Resources\ {
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
            "programId" => $programId,
            "participantId" => $participantId,
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
                ->andWhere($participantQb->expr()->eq("team.id", ":teamId"));

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

    public function aMetricAssignmentReportBelongsToClient(string $clientId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        $params = [
            "clientId" => $clientId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(ClientParticipant::class, "clientProgramParticipation")
                ->leftJoin("clientProgramParticipation.participant", "programParticipation")
                ->leftJoin("clientProgramParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"));

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

    public function allMetricAssignmentReportsInProgramParticipationBelongsToClient(string $clientId,
            string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(ClientParticipant::class, "clientProgramParticipation")
                ->andWhere($participantQb->expr()->eq("clientProgramParticipation.id", ":programParticipationId"))
                ->leftJoin("clientProgramParticipation.participant", "programParticipation")
                ->leftJoin("clientProgramParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder("metricAssignmentReport");
        $qb->select("metricAssignmentReport")
                ->leftJoin("metricAssignmentReport.metricAssignment", "metricAssignment")
                ->leftJoin("metricAssignment.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aMetricAssignmentReportBelongsToUser(string $userId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        $params = [
            "userId" => $userId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(UserParticipant::class, "userProgramParticipation")
                ->leftJoin("userProgramParticipation.participant", "programParticipation")
                ->leftJoin("userProgramParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"));

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

    public function allMetricAssignmentReportsInProgramParticipationBelongsToUser(
            string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("programParticipation.id")
                ->from(UserParticipant::class, "userProgramParticipation")
                ->andWhere($participantQb->expr()->eq("userProgramParticipation.id", ":programParticipationId"))
                ->leftJoin("userProgramParticipation.participant", "programParticipation")
                ->leftJoin("userProgramParticipation.user", "user")
                ->andWhere($participantQb->expr()->eq("user.id", ":userId"))
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