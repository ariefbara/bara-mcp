<?php

namespace Team\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Resources\Exception\RegularException;
use Team\Application\Service\Team\MemberRepository;
use Team\Application\Service\TeamMember\TeamMemberRepository;
use Team\Domain\Model\Team\Member;

class DoctrineMemberRepository extends EntityRepository implements MemberRepository, TeamMemberRepository
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

    public function aMemberOfTeam(string $firmId, string $clientId, string $memberid): Member
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'memberId' => $memberid,
        ];
        
        $qb = $this->createQueryBuilder("t_member");
        $qb->select("t_member")
                ->andWhere($qb->expr()->eq("t_member.id", ":memberId"))
                ->leftJoin("t_member.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
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

}
