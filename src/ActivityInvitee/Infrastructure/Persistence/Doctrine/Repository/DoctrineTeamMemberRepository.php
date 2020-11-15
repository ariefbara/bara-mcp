<?php

namespace ActivityInvitee\Infrastructure\Persistence\Doctrine\Repository;

use ActivityInvitee\{
    Application\Service\TeamMember\TeamMemberRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineTeamMemberRepository extends EntityRepository implements TeamMemberRepository
{

    public function aTeamMembershipCorrespondWithTeam(string $firmId, string $clientId, string $teamId): TeamMembership
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamId" => $teamId,
        ];

        $qb = $this->createQueryBuilder("teamMembership");
        $qb->select("teamMembership")
                ->andWhere($qb->expr()->eq("teamMembership.teamId", ":teamId"))
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team membership not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
