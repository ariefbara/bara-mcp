<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Domain\Model\Firm\Team\Member;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineTeamMemberRepository extends DoctrineEntityRepository implements TeamMemberRepository
{

    public function aTeamMemberCorrespondWithTeam(string $firmId, string $clientId, string $teamId): Member
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamId" => $teamId,
        ];

        $qb = $this->createQueryBuilder("teamMember");
        $qb->select("teamMember")
                ->leftJoin("teamMember.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("teamMember.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team member not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
