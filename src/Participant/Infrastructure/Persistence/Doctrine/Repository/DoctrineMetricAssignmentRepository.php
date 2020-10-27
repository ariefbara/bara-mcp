<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\MetricAssignmentRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\MetricAssignment,
    Domain\Model\UserParticipant
};
use Resources\Exception\RegularException;

class DoctrineMetricAssignmentRepository extends EntityRepository implements MetricAssignmentRepository
{
    
    public function ofId(string $metricAssignmentId): MetricAssignment
    {
        $metricAssignment = $this->findOneBy(["id" => $metricAssignmentId]);
        if (empty($metricAssignment)) {
            $errorDetail = "not found: metric assignment not found";
            throw RegularException::notFound($errorDetail);
        }
        return $metricAssignment;
    }

    public function aMetricAssignmentBelongsToClient(string $clientId, string $metricAssignmentId): MetricAssignment
    {
        $params = [
            "clientId" => $clientId,
            "metricAssignmentId" => $metricAssignmentId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->leftJoin("clientParticipant.participant", "t_participant")
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"));
        
        $qb = $this->createQueryBuilder("metricAssignment");
        $qb->select("metricAssignment")
                ->andWhere($qb->expr()->eq("metricAssignment.id", ":metricAssignmentId"))
                ->leftJoin("metricAssignment.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: metric assignment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aMetricAssignmentBelongsToUser(string $userId, string $metricAssignmentId): MetricAssignment
    {
        $params = [
            "userId" => $userId,
            "metricAssignmentId" => $metricAssignmentId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "userParticipant")
                ->leftJoin("userParticipant.participant", "t_participant")
                ->andWhere($participantQb->expr()->eq("userParticipant.userId", ":userId"));
        
        $qb = $this->createQueryBuilder("metricAssignment");
        $qb->select("metricAssignment")
                ->andWhere($qb->expr()->eq("metricAssignment.id", ":metricAssignmentId"))
                ->leftJoin("metricAssignment.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: metric assignment not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
