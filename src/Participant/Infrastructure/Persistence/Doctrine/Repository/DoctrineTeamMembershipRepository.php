<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Participant\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Participant\Application\Service\Firm\Client\TeamMembershipRepository;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Resources\Exception\RegularException;

class DoctrineTeamMembershipRepository extends EntityRepository implements TeamMembershipRepository, TeamMemberRepository
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
                ->leftJoin("teamMembership.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
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

    public function aTeamMembershipById(string $teamMembershipId): TeamMembership
    {
        return $this->findOneBy(["id" => $teamMembershipId]);
    }

}
