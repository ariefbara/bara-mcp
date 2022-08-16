<?php

namespace Team\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;
use Team\ {
    Application\Service\Team\MemberRepository,
    Domain\Model\Team\Member
};

class DoctrineMemberRepository extends EntityRepository implements MemberRepository
{

    public function aMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): Member
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder("t_member");
        $qb->select("t_member")
                ->leftJoin("t_member.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->andWhere($qb->expr()->eq("team.firmId", ":firmId"))
                ->leftJoin("t_member.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->andWhere($qb->expr()->eq("client.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: member not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function ofId(string $firmId, string $teamId, string $memberId): Member
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "memberId" => $memberId,
        ];

        $qb = $this->createQueryBuilder("t_member");
        $qb->select("t_member")
                ->andWhere($qb->expr()->eq("t_member.id", ":memberId"))
                ->leftJoin("t_member.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->andWhere($qb->expr()->eq("team.firmId", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: member not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
