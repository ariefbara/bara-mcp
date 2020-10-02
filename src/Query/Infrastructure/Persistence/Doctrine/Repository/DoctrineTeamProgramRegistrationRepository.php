<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Team\TeamProgramRegistrationRepository,
    Domain\Model\Firm\Team\Member,
    Domain\Model\Firm\Team\TeamProgramRegistration,
    Domain\Service\Firm\Team\TeamProgramRegistrationRepository as InterfaceForDomainService
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineTeamProgramRegistrationRepository extends EntityRepository implements TeamProgramRegistrationRepository, InterfaceForDomainService
{

    public function all(string $firmId, string $teamId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
        ];

        $qb = $this->createQueryBuilder("teamProgramRegistration");
        $qb->select("teamProgramRegistration")
                ->leftJoin("teamProgramRegistration.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId, string $teamId, string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "teamProgramRegistrationId" => $teamProgramRegistrationId,
        ];

        $qb = $this->createQueryBuilder("teamProgramRegistration");
        $qb->select("teamProgramRegistration")
                ->andWhere($qb->expr()->eq("teamProgramRegistration.id", ":teamProgramRegistrationId"))
                ->leftJoin("teamProgramRegistration.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("team.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team program registration not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aTeamProgramRegistrationOfTeamWhereClientIsMember(string $firmId, string $clientId,
            string $teamMembershipId, string $teamProgramRegsistrationId): TeamProgramRegistration
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
            "teamProgramRegistrationId" => $teamProgramRegsistrationId,
        ];
        
        $teamQb = $this->getEntityManager()->createQueryBuilder();
        $teamQb->select("t_team")
                ->from(Member::class, "teamMembership")
                ->andWhere($teamQb->expr()->eq("teamMembership.id", ":teamMembershipId"))
                ->leftJoin("teamMembership.client", "client")
                ->leftJoin("teamMembership.team", "t_team")
                ->andWhere($teamQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($teamQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("teamProgramRegsistration");
        $qb->select("teamProgramRegsistration")
                ->andWhere($qb->expr()->eq("teamProgramRegsistration.id", ":teamProgramRegistrationId"))
                ->leftJoin("teamProgramRegsistration.team", "team")
                ->andWhere($qb->expr()->in("team.id", $teamQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team program registration not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allTeamProgramRegistrationsOfTeamWhereClientIsMember(string $firmId, string $clientId,
            string $teamMembershipId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "teamMembershipId" => $teamMembershipId,
        ];
        
        $teamQb = $this->getEntityManager()->createQueryBuilder();
        $teamQb->select("t_team")
                ->from(Member::class, "teamMembership")
                ->andWhere($teamQb->expr()->eq("teamMembership.id", ":teamMembershipId"))
                ->leftJoin("teamMembership.team", "t_team")
                ->leftJoin("teamMembership.client", "client")
                ->andWhere($teamQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($teamQb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1);
        
        $qb = $this->createQueryBuilder("teamProgramRegsistration");
        $qb->select("teamProgramRegsistration")
                ->leftJoin("teamProgramRegsistration.team", "team")
                ->andWhere($qb->expr()->in("team.id", $teamQb->getDQL()))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function aProgramRegistrationOfTeam(string $teamId, string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $params = [
            "teamId" => $teamId, 
            "teamProgramRegistrationId" => $teamProgramRegistrationId, 
        ];
        
        $qb = $this->createQueryBuilder("teamProgramRegistration");
        $qb->select("teamProgramRegistration")
                ->andWhere($qb->expr()->eq("teamProgramRegistration.id", ":teamProgramRegistrationId"))
                ->leftJoin("teamProgramRegistration.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: team program registration not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProgramRegistrationsOfTeam(string $teamId, int $page, int $pageSize, ?bool $concludedStatus)
    {
        $params = [
            "teamId" => $teamId, 
        ];
        
        $qb = $this->createQueryBuilder("teamProgramRegistration");
        $qb->select("teamProgramRegistration")
                ->leftJoin("teamProgramRegistration.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->setParameters($params);
        
        if (isset($concludedStatus)) {
            $qb->andWhere($qb->expr()->eq("teamProgramRegistration.concluded", ":concludedStatus"))
                    ->setParameter("concludedStatus", $concludedStatus);
        }
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

}
