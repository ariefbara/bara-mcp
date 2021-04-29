<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Application\Service\TeamMember\OKRPeriodRepository;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\DedicatedMentorRepository;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Service\DataFinder;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;
use Query\Domain\Service\Firm\Program\Participant\WorksheetFinder;
use Query\Domain\Service\LearningMaterialFinder;
use Query\Domain\Service\ObjectiveProgressReportFinder;
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
    
    public function viewOKRPeriod(OKRPeriodRepository $okrPeriodRepository, string $okrPeriodId): OKRPeriod
    {
        return $this->programParticipation->viewOKRPeriod($okrPeriodRepository, $okrPeriodId);
    }
    public function viewAllOKRPeriod(OKRPeriodRepository $okrPeriodRepository, int $page, int $pageSize)
    {
        return $this->programParticipation->viewAllOKRPeriod($okrPeriodRepository, $page, $pageSize);
    }
    
    public function viewObjectiveProgressReport(ObjectiveProgressReportFinder $finder, string $objectiveProgressReportId): ObjectiveProgressReport
    {
        return $this->programParticipation->viewObjectiveProgressReport($finder, $objectiveProgressReportId);
    }
    public function viewAllObjectiveProgressReportsInObjective(
            ObjectiveProgressReportFinder $finder, string $objectivevId, int $page, int $pageSize)
    {
        return $this->programParticipation->viewAllObjectiveProgressReportsInObjective(
                $finder, $objectivevId, $page, $pageSize);
    }
    
    public function viewDedicatedMentor(DedicatedMentorRepository $dedicatedMentorRepository, string $dedicatedMentorId): DedicatedMentor
    {
        return $this->programParticipation->viewDedicatedMentor($dedicatedMentorRepository, $dedicatedMentorId);
    }
    public function viewAllDedicatedMentors(
            DedicatedMentorRepository $dedicatedMentorRepository, int $page, int $pageSize, ?bool $cancelledStatus)
    {
        return $this->programParticipation
                ->viewAllDedicatedMentors($dedicatedMentorRepository, $page, $pageSize, $cancelledStatus);
    }
    
    public function viewMissionComment(
            MissionCommentRepository $missionCommentRepository, string $missionCommentId): MissionComment
    {
        return $this->programParticipation->viewMissionComment($missionCommentRepository, $missionCommentId);
    }
    public function viewAllMissionComments(
            MissionCommentRepository $missionCommentRepository, string $missionId, int $page, int $pageSize)
    {
        return $this->programParticipation->viewAllMissionComments($missionCommentRepository, $missionId, $page, $pageSize);
    }
    
}
