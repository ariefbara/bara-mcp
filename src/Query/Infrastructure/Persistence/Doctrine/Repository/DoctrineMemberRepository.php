<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Auth\Firm\Client\TeamMembershipRepository;
use Query\Application\Auth\Firm\Team\MemberRepository as InterfaceForAuthorization;
use Query\Application\Service\Client\TeamMember\TeamMemberRepository as TeamMemberRepository3;
use Query\Application\Service\Firm\Program\TeamMemberRepository;
use Query\Application\Service\Firm\Team\MemberRepository;
use Query\Application\Service\TeamMember\TeamMemberRepository as TeamMemberRepository2;
use Query\Domain\Model\Firm\Team\Member;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineMemberRepository extends EntityRepository implements MemberRepository, InterfaceForAuthorization, TeamMembershipRepository, TeamMemberRepository, TeamMemberRepository2, TeamMemberRepository3
{

    public function aTeamMembershipOfClient(string $firmId, string $clientId, string $teamMembershipId): Member
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
        ];

        $qb = $this->createQueryBuilder("teamMembership");
        $qb->select("teamMembership")
                ->andWhere($qb->expr()->eq("teamMembership.id", ":teamMembershipId"))
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team membership not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allTeamMembershipsOfClient(string $firmId, string $clientId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
        ];

        $qb = $this->createQueryBuilder("teamMembership");
        $qb->select("teamMembership")
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("teamMembership.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
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
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: member not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(string $firmId, string $teamId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
        ];

        $qb = $this->createQueryBuilder("t_member");
        $qb->select("t_member")
                ->leftJoin("t_member.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfActiveTeamMemberCorrespondWithClient(string $firmId, string $teamId, string $clientId): bool
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "clientId" => $clientId,
        ];
        
        $qb = $this->createQueryBuilder("t_member");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("t_member.active", "true"))
                ->leftJoin("t_member.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "t_firm")
                ->andWhere($qb->expr()->eq("t_firm.id", ":firmId"))
                ->leftJoin("t_member.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "c_firm")
                ->andWhere($qb->expr()->eq("c_firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveTeamMemberWithAdminPriviledgeCorrespondWithClient(
            string $firmId, string $teamId, string $clientId): bool
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "clientId" => $clientId,
        ];
        
        $qb = $this->createQueryBuilder("t_member");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("t_member.active", "true"))
                ->andWhere($qb->expr()->eq("t_member.anAdmin", "true"))
                ->leftJoin("t_member.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "t_firm")
                ->andWhere($qb->expr()->eq("t_firm.id", ":firmId"))
                ->leftJoin("t_member.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "c_firm")
                ->andWhere($qb->expr()->eq("c_firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

    public function containRecordOfActiveTeamMembership(string $firmId, string $clientId, string $teamMembershipId): bool
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
        ];
        
        $qb = $this->createQueryBuilder("teamMembership");
        $qb->select("1")
                ->andWhere($qb->expr()->eq("teamMembership.id", ":teamMembershipId"))
                ->andWhere($qb->expr()->eq("teamMembership.active", "true"))
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        return !empty($qb->getQuery()->getResult());
    }

    public function aTeamMembershipCorrespondWithTeam(string $clientId, string $teamId): Member
    {
        $params = [
            "clientId" => $clientId,
            "teamId" => $teamId,
        ];
        
        $qb = $this->createQueryBuilder("teamMember");
        $qb->select("teamMember")
                ->leftJoin("teamMember.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("teamMember.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team member not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aTeamMemberInFirm(string $firmId, string $teamMemberId): Member
    {
        $params = [
            "firmId" => $firmId,
            "teamMemberId" => $teamMemberId,
        ];
        
        $qb = $this->createQueryBuilder("teamMember");
        $qb->select("teamMember")
                ->andWhere($qb->expr()->eq("teamMember.id", ":teamMemberId"))
                ->leftJoin("teamMember.team", "team")
                ->leftJoin("team.firm", "firm")
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

    public function allMembersOfTeam(string $firmId, string $teamId, int $page, int $pageSize, ?bool $activeStatus)
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
        ];
        
        $qb = $this->createQueryBuilder("teamMember");
        $qb->select("teamMember")
                ->leftJoin("teamMember.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        if (isset($activeStatus)) {
            $qb->andWhere($qb->expr()->eq("teamMember.active", ":activeStatus"))
                    ->setParameter("activeStatus", $activeStatus);
        }
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aTeamMemberOfClient(string $firmId, string $clientId, string $memberId): Member
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'memberId' => $memberId,
        ];
        
        $qb = $this->createQueryBuilder('t_member');
        $qb->select('t_member')
                ->andWhere($qb->expr()->eq('t_member.id', ':memberId'))
                ->leftJoin('t_member.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: team member not found');
        }
    }

    public function aTeamMemberOfClientCorrespondWithTeam(string $firmId, string $clientId, string $teamId): Member
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'teamId' => $teamId,
        ];
        
        $qb = $this->createQueryBuilder('t_member');
        $qb->select('t_member')
                ->leftJoin('t_member.team', 'team')
                ->andWhere($qb->expr()->eq('team.id', ':teamId'))
                ->leftJoin('t_member.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: team member not found');
        }
        
    }

}
