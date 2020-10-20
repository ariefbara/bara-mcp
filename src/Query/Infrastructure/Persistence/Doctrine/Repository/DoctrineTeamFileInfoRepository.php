<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Team\TeamFileInfoRepository,
    Domain\Model\Firm\Team\TeamFileInfo
};
use Resources\Exception\RegularException;

class DoctrineTeamFileInfoRepository extends EntityRepository implements TeamFileInfoRepository
{

    public function aFileInfoBelongsToTeam(string $teamId, string $teamFileInfoId): TeamFileInfo
    {
        $params = [
            "teamId" => $teamId,
            "teamFileInfoId" => $teamFileInfoId,
        ];

        $qb = $this->createQueryBuilder("teamFileInfo");
        $qb->select("teamFileInfo")
                ->andWhere($qb->expr()->eq("teamFileInfo.id", ":teamFileInfoId"))
                ->leftJoin("teamFileInfo.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team file info not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
