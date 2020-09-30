<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\ {
    Model\Firm\Program,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Team,
    Service\Firm\Program\Participant\WorksheetFinder
};

class TeamProgramParticipation
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
     * @var Participant
     */
    protected $programParticipation;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getProgram(): Program
    {
        return $this->programParticipation->getProgram();
    }

    public function getEnrolledTimeString(): string
    {
        return $this->programParticipation->getEnrolledTimeString();
    }

    public function isActive(): bool
    {
        return $this->programParticipation->isActive();
    }

    public function getNote(): ?string
    {
        return $this->programParticipation->getNote();
    }

    public function viewWorksheet(WorksheetFinder $worksheetFinder, string $worksheetId): Worksheet
    {
        return $this->programParticipation->viewWorksheet($worksheetFinder, $worksheetId);
    }

    public function viewAllWorksheets(WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        return $this->programParticipation->viewAllWorksheet($worksheetFinder, $page, $pageSize);
    }
    
    public function viewAllRootWorksheets(WorksheetFinder $worksheetFinder, int $page, int $pageSize)
    {
        return $this->programParticipation->viewAllRootWorksheets($worksheetFinder, $page, $pageSize);
    }
    
    public function viewAllBranchesWorksheets(WorksheetFinder $worksheetFinder, string $worksheetId, int $page, int $pageSize)
    {
        return $this->programParticipation->viewAllBranchWorksheets($worksheetFinder, $worksheetId, $page, $pageSize);
    }

}
