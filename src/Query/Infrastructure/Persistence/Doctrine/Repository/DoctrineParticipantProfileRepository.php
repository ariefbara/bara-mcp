<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Client\ParticipantProfileRepository as InterfaceForClient;
use Query\Application\Service\Firm\Program\ParticipantProfileRepository;
use Query\Application\Service\Firm\Team\ParticipantProfileRepository as InterfaceForTeam;
use Query\Application\Service\User\ParticipantProfileRepository as InterfaceForUser;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantProfileFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantProfileRepository as ParticipantProfileRepository2;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineParticipantProfileRepository extends BelongsToParticipantEntityRepository
        implements ParticipantProfileRepository, InterfaceForClient, InterfaceForUser, InterfaceForTeam, ParticipantProfileRepository2
{

    public function aParticipantProfileInProgram(string $firmId, string $programId, string $participantProfileId): ParticipantProfile
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantProfileId" => $participantProfileId,
        ];

        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->andWhere($qb->expr()->eq("participantProfile.id", ":participantProfileId"))
                ->leftJoin("participantProfile.participant", "participant")
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1)
                ->setParameters($params);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allProfilesBelongsToParticipantInProgram(
            string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "programId" => $programId,
            "participantId" => $participantId,
        ];

        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->eq("participant.id", ":participantId"))
                ->leftJoin("participant.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("program.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aParticipantProfileBelongsToClientCorrespondWithProgramsProfileForm(
            string $firmId, string $clientId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "participantId" => $programParticipationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("a_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->leftJoin("clientParticipant.participant", "a_participant")
                ->andWhere($participantQb->expr()->eq("a_participant.id", ":participantId"))
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allParticipantProfilesBelongsToParticipant(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
            "participantId" => $programParticipationId,
        ];

        $participantQb = $this->getEntityManager()->createQueryBuilder();
        $participantQb->select("a_participant.id")
                ->from(ClientParticipant::class, "clientParticipant")
                ->leftJoin("clientParticipant.participant", "a_participant")
                ->andWhere($participantQb->expr()->eq("a_participant.id", ":participantId"))
                ->leftJoin("clientParticipant.client", "client")
                ->andWhere($participantQb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($participantQb->expr()->eq("firm.id", ":firmId"));

        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $participantQb->getDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aParticipantProfileBelongsToUserCorrespondWithProgramsProfileForm(string $userId,
            string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $this->getUserParticipantIdDQL()))
                ->leftJoin("participantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allParticipantProfilesBelongsToUser(
            string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
        ];
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $this->getUserParticipantIdDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function aParticipantProfileBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programParticipationId, string $programsProfileFormId): ParticipantProfile
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "programParticipationId" => $programParticipationId,
            "programsProfileFormId" => $programsProfileFormId,
        ];
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $this->getTeamParticipantIdDQL()))
                ->leftJoin("participantProfile.programsProfileForm", "programsProfileForm")
                ->andWhere($qb->expr()->eq("programsProfileForm.id", ":programsProfileFormId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: participant profile not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allParticipantProfilesBelongsToTeamCorrespondWithProgramsProfileForm(
            string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "teamId" => $teamId,
            "programParticipationId" => $programParticipationId,
        ];
        $qb = $this->createQueryBuilder("participantProfile");
        $qb->select("participantProfile")
                ->andWhere($qb->expr()->eq("participantProfile.removed", "false"))
                ->leftJoin("participantProfile.participant", "participant")
                ->andWhere($qb->expr()->in("participant.id", $this->getTeamParticipantIdDQL()))
                ->setParameters($params);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function allParticipantProfilesInInProgram(string $programId, ParticipantProfileFilter $filter)
    {
        
    }

    public function aParticipantProfileBelongsInProgram(string $programId, string $participantProfileId): ParticipantProfile
    {
        $parameters = [
            'programId' => $programId,
            'id' => $participantProfileId
        ];

        $qb = $this->createQueryBuilder('participantProfile');
        $qb->select('participantProfile')
                ->andWhere($qb->expr()->eq('participantProfile.id', ':id'))
                ->leftJoin('participantProfile.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('participant profile not found');
        }
    }

    public function allParticipantProfilesBelongsInProgram(string $programId, ParticipantProfileFilter $filter)
    {
        $parameters = [
            'programId' => $programId,
        ];

        $qb = $this->createQueryBuilder('participantProfile');
        $qb->select('participantProfile')
                ->leftJoin('participantProfile.participant', 'participant')
                ->leftJoin('participant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameters($parameters);

        $participantIdFilter = $filter->getParticipantId();
        if ($participantIdFilter) {
            $qb->andWhere($qb->expr()->eq('participant.id', ':participantId'))
                    ->setParameter('participantId', $participantIdFilter);
        }

        $page = $filter->getPaginationFilter()->getPage();
        $pageSize = $filter->getPaginationFilter()->getPageSize();
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
