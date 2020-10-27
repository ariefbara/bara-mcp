<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\MetricAssignment\MetricAssignmentReportRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\UserParticipant
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineMetricAssignmentReportRepository extends EntityRepository implements MetricAssignmentReportRepository
{
    
    public function add(MetricAssignmentReport $metricAssignmentReport): void
    {
        $em = $this->getEntityManager();
        $em->persist($metricAssignmentReport);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $metricAssignmentReportId): MetricAssignmentReport
    {
        $metricAssignmentReport = $this->findOneBy(["id" => $metricAssignmentReportId]);
        if (empty($metricAssignmentReport)) {
            $errorDetail = "not found: metric assignment report not found";
            throw RegularException::notFound($errorDetail);
        }
        return $metricAssignmentReport;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aMetricAssignmentReportBelongsToClient(
            string $clientId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        $params = [
            "clientId" => $clientId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->leftJoin("clientParticipant.participant", "t_participant")
                ->leftJoin("clientParticipant.client", "client")
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

    public function aMetricAssignmentReportBelongsToUser(string $userId, string $metricAssignmentReportId): MetricAssignmentReport
    {
        $params = [
            "userId" => $userId,
            "metricAssignmentReportId" => $metricAssignmentReportId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "userParticipant")
                ->leftJoin("userParticipant.participant", "t_participant")
                ->andWhere($participantQb->expr()->eq("userParticipant.userId", ":userId"));
        
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

}
