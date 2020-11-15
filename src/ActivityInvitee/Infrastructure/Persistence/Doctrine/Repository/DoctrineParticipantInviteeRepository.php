<?php

namespace ActivityInvitee\Infrastructure\Persistence\Doctrine\Repository;

use ActivityInvitee\ {
    Application\Service\Participant\ActivityInvitationRepository,
    Domain\DependencyModel\Firm\Client\ProgramParticipation as ClientParticipant,
    Domain\DependencyModel\User\ProgramParticipation as UserParticipant,
    Domain\Model\ParticipantInvitee
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineParticipantInviteeRepository extends EntityRepository implements ActivityInvitationRepository
{

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function anInvitationBelongsToClient(string $firmId, string $clientId, string $invitationId): ParticipantInvitee
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "invitationId" => $invitationId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->leftJoin("clientParticipant.participant", "t_participant")
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->andWhere($participantQb->expr()->eq("client.firmId", ":firmId"));
        
        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $invitationId): ParticipantInvitee
    {
        $invitation = $this->findOneBy(["id" => $invitationId]);
        if (empty($invitation)) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
        return $invitation;
    }

    public function anInvitationBelongsToUser(string $userId, string $invitationId): ParticipantInvitee
    {
        $params = [
            "userId" => $userId,
            "invitationId" => $invitationId,
        ];
        
        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("t_participant.id")
                ->from(UserParticipant::class, "userParticipant")
                ->leftJoin("userParticipant.participant", "t_participant")
                ->andWhere($participantQb->expr()->eq("userParticipant.userId", ":userId"));
        
        $qb = $this->createQueryBuilder("invitation");
        $qb->select("invitation")
                ->andWhere($qb->expr()->eq("invitation.id", ":invitationId"))
                ->leftJoin("invitation.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: invitation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
