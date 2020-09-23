<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\Firm\TeamRepository,
    Domain\Model\Firm\Team
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineTeamRepository extends EntityRepository implements TeamRepository
{

    public function all(string $firmId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
        ];

        $qb = $this->createQueryBuilder("team");
        $qb->select("team")
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $teamId): Team
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
        ];

        $qb = $this->createQueryBuilder("team");
        $qb->select("team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
