<?php

namespace Query\Domain\Model\Firm\Team;

use DateTimeImmutable;
use Query\Domain\ {
    Model\Firm\Client,
    Model\Firm\Program,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Team,
    Service\Firm\ClientFinder,
    Service\Firm\Program\Participant\WorksheetFinder,
    Service\Firm\ProgramFinder,
    Service\Firm\Team\TeamFileInfoFinder,
    Service\Firm\Team\TeamProgramParticipationFinder
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

}
