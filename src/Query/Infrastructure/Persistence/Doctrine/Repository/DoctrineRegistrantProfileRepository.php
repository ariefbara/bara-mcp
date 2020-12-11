<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Client\RegistrantProfileRepository as InterfaceForClient;
use Query\Application\Service\Firm\Program\RegistrantProfileRepository;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineRegistrantProfileRepository extends EntityRepository implements RegistrantProfileRepository, InterfaceForClient
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

}
