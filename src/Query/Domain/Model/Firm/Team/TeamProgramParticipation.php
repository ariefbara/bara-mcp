<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Service\DataFinder;
use Query\Domain\Service\Firm\Program\Participant\WorksheetFinder;
use Query\Domain\Service\LearningMaterialFinder;
use Resources\Application\Event\ContainEvents;

class TeamProgramParticipation implements ContainEvents
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
    
    public function teamEquals(Team $team): bool
    {
        return $this->team === $team;
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
    
    public function getMetricAssignment(): ?MetricAssignment
    {
        return $this->programParticipation->getMetricAssignment();
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

    public function viewAllBranchesWorksheets(WorksheetFinder $worksheetFinder, string $worksheetId, int $page,
            int $pageSize)
    {
        return $this->programParticipation->viewAllBranchWorksheets($worksheetFinder, $worksheetId, $page, $pageSize);
    }

    public function viewLearningMaterial(
            LearningMaterialFinder $learningMaterialFinder, string $learningMaterialId): LearningMaterial
    {
        return $this->programParticipation->viewLearningMaterial($learningMaterialFinder, $learningMaterialId);
    }

    public function pullRecordedEvents(): array
    {
        return $this->programParticipation->pullRecordedEvents();
    }
    
    public function viewSummary(DataFinder $dataFinder): array
    {
        return $this->programParticipation->viewSummary($dataFinder);
    }
    
}
