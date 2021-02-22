<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Auth\TeamRegistrantRepository as InterfaceForAuthorization;
use Query\Application\Service\Firm\Team\TeamProgramRegistrationRepository;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration;
use Query\Domain\Service\Firm\Team\TeamProgramRegistrationRepository as InterfaceForDomainService;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineTeamProgramRegistrationRepository extends EntityRepository implements TeamProgramRegistrationRepository, InterfaceForDomainService,
        InterfaceForAuthorization
{

    public function all(string $firmId, string $teamId, int $page, int $pageSize, ?bool $concludedStatus)
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

        if (isset($concludedStatus)) {
            $qb->leftJoin('teamProgramRegistration.programRegistration', 'registrant')
                ->andWhere($qb->expr()->eq("registrant.concluded", ":concludedStatus"))
                ->setParameter("concludedStatus", $concludedStatus);
        }
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

    public function containRecordOfUnconcludedRegistrationToProgram(string $firmId, string $teamId, string $programId): bool
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "programId" => $programId,
        ];
        
        $qb = $this->createQueryBuilder("teamRegistrant");
        $qb->select("1")
                ->leftJoin("teamRegistrant.team", "team")
                ->andWhere($qb->expr()->eq("team.id", ":teamId"))
                ->leftJoin("teamRegistrant.programRegistration", "registrant")
                ->andWhere($qb->expr()->eq("registrant.concluded", "false"))
                ->leftJoin("registrant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

}
