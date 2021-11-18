<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\TeamRepository;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Task\Dependency\Firm\TeamFilter;
use Query\Domain\Task\Dependency\Firm\TeamRepository as TeamRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineTeamRepository extends EntityRepository implements TeamRepository, TeamRepository2
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

    public function aTeamInFirm(string $firmId, string $id): Team
    {
        $parameters = [
            'firmId' => $firmId,
            'id' => $id,
        ];

        $qb = $this->createQueryBuilder('team');
        $qb->select('team')
                ->andWhere($qb->expr()->eq('team.id', ':id'))
                ->leftJoin('team.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: team not found');
        }
    }

    public function allTeamInFirm(string $firmId, TeamFilter $teamFilter)
    {
        $parameters = [
            'firmId' => $firmId,
        ];

        $qb = $this->createQueryBuilder('team');
        $qb->select('team')
                ->leftJoin('team.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters);

        if (!empty($teamFilter->getName())) {
            $qb->andWhere($qb->expr()->like('team.name', ':name'))
                    ->setParameter('name', "%{$teamFilter->getName()}%");
        }

        return PaginatorBuilder::build($qb->getQuery(), $teamFilter->getPage(), $teamFilter->getPageSize());
    }

}
