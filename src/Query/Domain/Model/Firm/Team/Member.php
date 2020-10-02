<?php

namespace Query\Domain\Model\Firm\Team;

use DateTimeImmutable;
use Query\{
    Domain\Model\Firm\Client,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Model\Firm\Team,
    Domain\Service\Firm\ClientFinder,
    Domain\Service\Firm\Program\ConsultationSetup\ConsultationRequestFinder,
    Domain\Service\Firm\Program\Participant\ConsultationSessionFinder,
    Domain\Service\Firm\Program\Participant\WorksheetFinder,
    Domain\Service\Firm\ProgramFinder,
    Domain\Service\Firm\Team\TeamFileInfoFinder,
    Domain\Service\Firm\Team\TeamProgramParticipationFinder,
    Domain\Service\Firm\Team\TeamProgramRegistrationFinder,
    Infrastructure\QueryFilter\ConsultationRequestFilter,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};
use Resources\Exception\RegularException;

class Member
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string||null
     */
    protected $position;

    /**
     *
     * @var bool
     */
    protected $anAdmin;

    /**
     *
     * @var bool
     */
    protected $active;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    protected function __construct()
    {
        ;
    }

    protected function assertAnAdmin(): void
    {
        if (!$this->anAdmin) {
            $errorDetail = "forbidden: only team admin can make this requests";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this requests";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function isAnAdmin(): bool
    {
        return $this->anAdmin;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getJoinTimeString(): string
    {
        return $this->joinTime->format("Y-m-d H:i:s");
    }

    public function viewClientByEmail(ClientFinder $clientFinder, string $clientEmail): Client
    {
        $this->assertAnAdmin();
        $this->assertActive();
        return $clientFinder->findByEmail($this->client->getFirm()->getId(), $clientEmail);
    }

    public function viewTeamProgramRegistration(TeamProgramRegistrationFinder $teamProgramRegistrationFinder,
            string $teamProgramRegistrationId): TeamProgramRegistration
    {
        $this->assertActive();
        return $teamProgramRegistrationFinder
                        ->findProgramRegistrationBelongsToTeam($this->team, $teamProgramRegistrationId);
    }

    public function viewAllTeamProgramRegistration(TeamProgramRegistrationFinder $teamProgramRegistrationFinder,
            int $page, int $pageSize, ?bool $concludedStatus)
    {
        $this->assertActive();
        return $teamProgramRegistrationFinder
                        ->findAllProgramRegistrationsBelongsToTeam($this->team, $page, $pageSize, $concludedStatus);
    }

    public function viewTeamProgramParticipation(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId): TeamProgramParticipation
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId);
    }

    public function viewAllProgramParticipation(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder->findAllProgramParticipationsBelongsToTeam($this->team, $page, $pageSize);
    }

    public function viewWorksheet(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, string $worksheetId): Worksheet
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewWorksheet($worksheetFinder, $worksheetId);
    }

    public function viewAllWorksheets(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewAllWorksheets($worksheetFinder, $page, $pageSize);
    }

    public function viewAllRootWorksheets(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewAllRootWorksheets($worksheetFinder, $page, $pageSize);
    }

    public function viewAllBranchWorksheets(
            TeamProgramParticipationFinder $teamProgramParticipationFinder, string $teamProgramParticipationId,
            WorksheetFinder $worksheetFinder, string $worksheetId, int $page, int $pageSize)
    {
        $this->assertActive();
        return $teamProgramParticipationFinder
                        ->findProgramParticipationBelongsToTeam($this->team, $teamProgramParticipationId)
                        ->viewAllBranchesWorksheets($worksheetFinder, $worksheetId, $page, $pageSize);
    }

    public function viewTeamFileInfo(TeamFileInfoFinder $teamFileInfoFinder, string $teamFileInfoId): TeamFileInfo
    {
        $this->assertActive();
        return $teamFileInfoFinder->findFileInfoBelongsToTeam($this->team, $teamFileInfoId);
    }

    public function viewProgram(ProgramFinder $programFinder, string $programId): Program
    {
        $this->assertActive();
        return $programFinder->findProgramAvaiableForTeam($this->team, $programId);
    }

    public function viewAllAvailablePrograms(ProgramFinder $programFinder, int $page, int $pageSize)
    {
        $this->assertActive();
        return $programFinder->findAllProgramsAvailableForTeam($this->team, $page, $pageSize);
    }

    public function viewConsultationRequest(
            ConsultationRequestFinder $consultationRequestFinder, string $teamProgramParticipationId,
            string $consultationRequestId)
    {
        $this->assertActive();
        return $consultationRequestFinder->findConsultationRequestBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $consultationRequestId);
    }

    public function viewAllConsultationRequest(
            ConsultationRequestFinder $consultationRequestFinder, string $teamProgramParticipationId, int $page,
            int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        $this->assertActive();
        return $consultationRequestFinder->findAllConsultationRequestsBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $page, $pageSize, $consultationRequestFilter);
    }

    public function viewConsultationSession(
            ConsultationSessionFinder $consultationSessionFinder, string $teamProgramParticipationId,
            string $consultationSessionId): ConsultationSession
    {
        $this->assertActive();
        return $consultationSessionFinder->findConsultationSessionBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $consultationSessionId);
    }

    public function viewAllConsultationSession(
            ConsultationSessionFinder $consultationSessionFinder, string $teamProgramParticipationId, int $page,
            int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter)
    {
        $this->assertActive();
        return $consultationSessionFinder->findAllConsultationSessionBelongsToTeam(
                        $this->team, $teamProgramParticipationId, $page, $pageSize, $consultationSessionFilter);
    }

}
