<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;

class BelongsToParticipantEntityRepository extends EntityRepository
{
    protected function getClientParticipantIdDQL()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("clientParticipant_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->andWhere($qb->expr()->eq("clientParticipant.id", ":programParticipationId"))
                ->leftJoin("clientParticipant.participant", "clientParticipant_participant")
                ->leftJoin("clientParticipant.client", "clientParticipant_client")
                ->andWhere($qb->expr()->eq("clientParticipant_client.id", ":clientId"))
                ->leftJoin("clientParticipant_client.firm", "clientParticipant_firm")
                ->andWhere($qb->expr()->eq("clientParticipant_firm.id", ":firmId"))
                ->setMaxResults(1);
        return $qb->getDQL();
    }
    
    protected function getTeamParticipantIdDQL()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("teamParticipant_participant.id")
                ->from(TeamProgramParticipation::class, "teamParticipant")
                ->andWhere($qb->expr()->eq("teamParticipant.id", ":programParticipationId"))
                ->leftJoin("teamParticipant.programParticipation", "teamParticipant_participant")
                ->leftJoin("teamParticipant.team", "teamParticipant_team")
                ->andWhere($qb->expr()->eq("teamParticipant_team.id", ":teamId"))
                ->leftJoin("teamParticipant_team.firm", "teamParticipant_firm")
                ->andWhere($qb->expr()->eq("teamParticipant_firm.id", ":firmId"))
                ->setMaxResults(1);
        return $qb->getDQL();
    }
    
    protected function getUserParticipantIdDQL()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("userParticipant_participant.id")
                ->from(UserParticipant::class, "userParticipant")
                ->andWhere($qb->expr()->eq("userParticipant.id", ":programParticipationId"))
                ->leftJoin("userParticipant.participant", "userParticipant_participant")
                ->leftJoin("userParticipant.user", "userParticipant_user")
                ->andWhere($qb->expr()->eq("userParticipant_user.id", ":userId"))
                ->setMaxResults(1);
        return $qb->getDQL();
    }
}
