<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\ {
    Application\Service\Participant\ParticipantActivityRepository,
    Domain\DependencyModel\Firm\Client\ProgramParticipation as ClientParticipant,
    Domain\DependencyModel\User\ProgramParticipation as UserParticipant,
    Domain\Model\ParticipantActivity
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineParticipantActivityRepository extends EntityRepository implements ParticipantActivityRepository
{

    public function add(ParticipantActivity $participantActivity): void
    {
        $em = $this->getEntityManager();
        $em->persist($participantActivity);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aParticipantActivityBelongsToClient(string $firmId, string $clientId, string $participantActivityId): ParticipantActivity
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "participantActivityId" => $participantActivityId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->leftJoin("programParticipation.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->andWhere($participantQb->expr()->eq("client.firmId", ":firmId"));
        
        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->andWhere($qb->expr()->eq("participantActivity.id", ":participantActivityId"))
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aParticipantActivityBelongsToUser(string $userId, string $participantActivityId): ParticipantActivity
    {
        $params = [
            "userId" => $userId,
            "participantActivityId" => $participantActivityId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "programParticipation")
                ->leftJoin("programParticipation.participant", "t_participant")
                ->andWhere($participantQb->expr()->eq("programParticipation.userId", ":userId"));
        
        $qb = $this->createQueryBuilder("participantActivity");
        $qb->select("participantActivity")
                ->andWhere($qb->expr()->eq("participantActivity.id", ":participantActivityId"))
                ->leftJoin("participantActivity.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant activity not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $participantActivityId): ParticipantActivity
    {
        $participantActivity = $this->findOneBy(["id" => $participantActivityId]);
        if (empty($participantActivity)) {
            $errorDetail = "not found: participant activity not found";
            throw RegularException::notFound($errorDetail);
        }
        return $participantActivity;
    }

}
