<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\Domain\ {
    Model\Firm\Team\TeamProgramParticipation,
    Service\Firm\Team\TeamProgramParticipationRepository
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineTeamProgramParticipationRepository extends EntityRepository implements TeamProgramParticipationRepository
{

    public function all(string $teamId, int $page, int $pageSize)
    {
        $params = [
            "teamId" => $teamId,
        ];

        $qb = $this->createQueryBuilder("teamProgramParticipation");
        $qb->select("teamProgramParticipation")
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $teamId, string $teamProgramParticipationId): TeamProgramParticipation
    {
        $params = [
            "teamId" => $teamId,
            "teamProgramParticipationId" => $teamProgramParticipationId,
        ];

        $qb = $this->createQueryBuilder("teamProgramParticipation");
        $qb->select("teamProgramParticipation")
                ->andWhere($qb->expr()->eq("teamProgramParticipation.id", ":teamProgramParticipationId"))
                ->leftJoin("teamProgramParticipation.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}