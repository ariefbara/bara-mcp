<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration;
use Query\Domain\Model\User\UserRegistrant;

abstract class BelongsToRegistrantEntityRepository extends EntityRepository
{
    protected function getClientRegistrantIdDQL()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("clientRegistrant_registrant.id")
                ->from(ClientRegistrant::class, "clientRegistrant")
                ->andWhere($qb->expr()->eq("clientRegistrant.id", ":programRegistrationId"))
                ->leftJoin("clientRegistrant.registrant", "clientRegistrant_registrant")
                ->leftJoin("clientRegistrant.client", "clientRegistrant_client")
                ->andWhere($qb->expr()->eq("clientRegistrant_client.id", ":clientId"))
                ->leftJoin("clientRegistrant_client.firm", "clientRegistrant_firm")
                ->andWhere($qb->expr()->eq("clientRegistrant_firm.id", ":firmId"))
                ->setMaxResults(1);
        return $qb->getDQL();
    }
    
    protected function getTeamRegistrantIdDQL()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("teamRegistrant_registrant.id")
                ->from(TeamProgramRegistration::class, "teamRegistrant")
                ->andWhere($qb->expr()->eq("teamRegistrant.id", ":programRegistrationId"))
                ->leftJoin("teamRegistrant.programRegistration", "teamRegistrant_registrant")
                ->leftJoin("teamRegistrant.team", "teamRegistrant_team")
                ->andWhere($qb->expr()->eq("teamRegistrant_team.id", ":teamId"))
                ->leftJoin("teamRegistrant_team.firm", "teamRegistrant_firm")
                ->andWhere($qb->expr()->eq("teamRegistrant_firm.id", ":firmId"))
                ->setMaxResults(1);
        return $qb->getDQL();
    }
    
    protected function getUserRegistrantIdDQL()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select("userRegistrant_registrant.id")
                ->from(UserRegistrant::class, "userRegistrant")
                ->andWhere($qb->expr()->eq("userRegistrant.id", ":programRegistrationId"))
                ->leftJoin("userRegistrant.registrant", "userRegistrant_registrant")
                ->leftJoin("userRegistrant.user", "userRegistrant_user")
                ->andWhere($qb->expr()->eq("userRegistrant_user.id", ":userId"))
                ->setMaxResults(1);
        return $qb->getDQL();
    }
}
