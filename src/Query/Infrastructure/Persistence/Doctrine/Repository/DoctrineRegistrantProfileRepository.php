<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Client\RegistrantProfileRepository as InterfaceForClient;
use Query\Application\Service\Firm\Program\RegistrantProfileRepository;
use Query\Application\Service\Firm\Team\RegistrantProfileRepository as InterfaceForTeam;
use Query\Application\Service\User\RegistrantProfileRepository as InterfaceForUser;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineRegistrantProfileRepository extends BelongsToRegistrantEntityRepository
        implements RegistrantProfileRepository, InterfaceForClient, InterfaceForTeam, InterfaceForUser
{

    public function aRegistrantProfileInProgram(string $firmId, string $programId, string $registrantProfileId): RegistrantProfile
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "registrantProfileId" => $registrantProfileId,
        ];

        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.id", ":registrantProfileId"))
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->leftJoin("registrant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allRegistrantProfilesInBelongsToRegistrant(
            string $firmId, string $programId, string $registrantId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "registrantId" => $registrantId,
        ];

        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->eq("registrant.id", ":registrantId"))
                ->leftJoin("registrant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aRegistrantProfileBelongsToClientCorrespondWithProgramsProfileForm(
            string $firmId, string $clientId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "registrantId" => $programRegistrationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];

        $registrantQb = $this->getEntityManager()->createQueryBuilder();
        $registrantQb->select("a_registrant.id")
                ->from(ClientRegistrant::class, "clientRegistrant")
                ->leftJoin("clientRegistrant.registrant", "a_registrant")
                ->andWhere($registrantQb->expr()->eq("a_registrant.id", ":registrantId"))
                ->leftJoin("clientRegistrant.client", "client")
                ->andWhere($registrantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($registrantQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.id", $registrantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allRegistrantProfilesInProgramRegistrationBelongsToClient(
            string $firmId, string $clientId, string $programRegistrationId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "registrantId" => $programRegistrationId,
        ];

        $registrantQb = $this->getEntityManager()->createQueryBuilder();
        $registrantQb->select("a_registrant.id")
                ->from(ClientRegistrant::class, "clientRegistrant")
                ->leftJoin("clientRegistrant.registrant", "a_registrant")
                ->andWhere($registrantQb->expr()->eq("a_registrant.id", ":registrantId"))
                ->leftJoin("clientRegistrant.client", "client")
                ->andWhere($registrantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($registrantQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.id", $registrantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aRegistrantProfileBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "programRegistrationId" => $programRegistrationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];
        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.id", $this->getTeamRegistrantIdDQL()))
                ->leftJoin("registrantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allRegistrantProfilesBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programRegistrationId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "programRegistrationId" => $programRegistrationId,
        ];
        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.id", $this->getTeamRegistrantIdDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aRegistrantProfileBelongsToUserCorrespondWithProgramsProfileForm(string $userId,
            string $programRegistrationId, string $programsProfileFormId): RegistrantProfile
    {
        $params = [
            "userId" => $userId,
            "programRegistrationId" => $programRegistrationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];
        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.id", $this->getUserRegistrantIdDQL()))
                ->leftJoin("registrantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: registrant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allRegistrantProfilesBelongsToUser(
            string $userId, string $programRegistrationId, int $page, int $pageSize)
    {
        $params = [
            "userId" => $userId,
            "programRegistrationId" => $programRegistrationId,
        ];
        $qb = $this->createQueryBuilder("registrantProfile");
        $qb->select("registrantProfile")
                ->andWhere($qb->expr()->eq("registrantProfile.removed", "false"))
                ->leftJoin("registrantProfile.registrant", "registrant")
                ->andWhere($qb->expr()->in("registrant.id", $this->getUserRegistrantIdDQL()))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
