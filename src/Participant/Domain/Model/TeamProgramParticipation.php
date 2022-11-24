<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Client\AssetBelongsToTeamInterface;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Participant\Domain\Model\Participant\ParticipantProfile;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Participant\Domain\Task\Participant\ParticipantTask;
use Resources\Application\Event\ContainEvents;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class TeamProgramParticipation implements AssetBelongsToTeamInterface, ContainEvents
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

    protected function __construct()
    {
        
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->team === $team;
    }

    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->programParticipation->isActiveParticipantOfProgram($program);
    }

    public function submitRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData,
            TeamMembership $teamMember): Worksheet
    {
        return $this->programParticipation->createRootWorksheet($worksheetId, $name, $mission, $formRecordData,
                        $teamMember);
    }

    public function submitBranchWorksheet(
            Worksheet $worksheet, string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData,
            TeamMembership $teamMember): Worksheet
    {
        return $this->programParticipation->submitBranchWorksheet(
                        $worksheet, $worksheetId, $name, $mission, $formRecordData, $teamMember);
    }

    public function quit(): void
    {
        $this->programParticipation->quit();
    }

    public function submitConsultationRequest(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            ConsultationRequestData $consultationRequestData, TeamMembership $teamMember): ConsultationRequest
    {
        return $this->programParticipation->submitConsultationRequest(
                        $consultationRequestId, $consultationSetup, $consultant, $consultationRequestData, $teamMember);
    }

    public function changeConsultationRequestTime(
            string $consultationRequestId, ConsultationRequestData $consultationRequestData, TeamMembership $teamMember): void
    {
        $this->programParticipation->changeConsultationRequestTime(
                $consultationRequestId, $consultationRequestData, $teamMember);
    }

    public function acceptOfferedConsultationRequest(string $consultationRequestId, TeamMembership $teamMember): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->programParticipation->acceptOfferedConsultationRequest(
                $consultationRequestId, $consultationSessionId, $teamMember);
    }

    public function pullRecordedEvents(): array
    {
        return $this->programParticipation->pullRecordedEvents();
    }

    public function ownAllAttachedFileInfo(MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): bool
    {
        $ownedAllFileInfo = true;
        foreach ($metricAssignmentReportDataProvider->iterateAllAttachedFileInfo() as $fileInfo) {
            $ownedAllFileInfo = $ownedAllFileInfo && $fileInfo->belongsToTeam($this->team);
        }
        return $ownedAllFileInfo;
    }

    public function submitMetricAssignmentReport(
            string $metricAssignmentReportId, DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): MetricAssignmentReport
    {
        return $this->programParticipation->submitMetricAssignmentReport(
                        $metricAssignmentReportId, $observationTime, $metricAssignmentReportDataProvider);
    }
    
    public function submitProfile(ProgramsProfileForm $programsProfileForm, FormRecordData $formRecordData): void
    {
        $this->programParticipation->submitProfile($programsProfileForm, $formRecordData);
    }
    
    public function removeProfile(ParticipantProfile $profile): void
    {
        $this->programParticipation->removeProfile($profile);
    }
    
    public function createOKRPeriod(string $okrPeriodId, OKRPeriodData $okrPeriodData): OKRPeriod
    {
        return $this->programParticipation->createOKRPeriod($okrPeriodId, $okrPeriodData);
    }
    public function updateOKRPeriod(OKRPeriod $okrPeriod, OKRPeriodData $okrPeriodData): void
    {
        $this->programParticipation->updateOKRPeriod($okrPeriod, $okrPeriodData);
    }
    public function cancelOKRPeriod(OKRPeriod $okrPeriod): void
    {
        $this->programParticipation->cancelOKRPeriod($okrPeriod);
    }
    
    public function submitObjectiveProgressReport(
            Objective $objective, string $objectiveProgressReportId, 
            ObjectiveProgressReportData $objectiveProgressReportData): ObjectiveProgressReport
    {
        return $this->programParticipation->submitObjectiveProgressReport(
                $objective, $objectiveProgressReportId, $objectiveProgressReportData);
    }
    public function updateObjectiveProgressReport(
            ObjectiveProgressReport $objectiveProgressReport, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $this->programParticipation->updateObjectiveProgressReport($objectiveProgressReport, $objectiveProgressReportData);
    }
    public function cancelObjectiveProgressReportSubmission(ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->programParticipation->cancelObjectiveProgressReportSubmission($objectiveProgressReport);
    }
    
    public function executeParticipantTask(ITaskExecutableByParticipant $task): void
    {
        $this->programParticipation->executeParticipantTask($task);
    }
    
    public function assertBelongsToTeam(Team $team): void
    {
        if ($this->team !== $team) {
            throw RegularException::forbidden('forbidden: participant doesn\'t belongs to team');
        }
    }
    
    public function executeTask(ParticipantTask $task, $payload): void
    {
        $this->programParticipation->executeTask($task, $payload);
    }

}
