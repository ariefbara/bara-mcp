<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Client;
use Participant\Domain\DependencyModel\Firm\Client\TeamMember\MemberComment;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Event\EventTriggeredByTeamMember;
use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\ConsultationSession;
use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Participant\Domain\Model\Participant\ParticipantProfile;
use Participant\Domain\Model\Participant\ViewLearningMaterialActivityLog;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Participant\Domain\Model\TeamProgramParticipation;
use Participant\Domain\Model\TeamProgramRegistration;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Participant\Domain\Task\Participant\ParticipantTask;
use Resources\Application\Event\ContainEvents;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Exception\RegularException;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class TeamMembership extends EntityContainEvents
{

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }
    protected function assertAssetBelongsToTeam(AssetBelongsToTeamInterface $assetBelongsToTeam): void
    {
        if (!$assetBelongsToTeam->belongsToTeam($this->team)) {
            $errorDetail = "forbidden: you can only manage asset belongs to your team";
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

//    public function registerTeamToProgram(string $teamProgramRegistrationId, Program $program): TeamProgramRegistration
//    {
//        $this->assertActive();
//        return $this->team->registerToProgram($teamProgramRegistrationId, $program);
//    }

    public function cancelTeamprogramRegistration(TeamProgramRegistration $teamProgramRegistration): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramRegistration);
        $teamProgramRegistration->cancel();
    }

    public function quitTeamProgramParticipation(TeamProgramParticipation $teamProgramParticipation): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        $teamProgramParticipation->quit();
    }

    public function submitRootWorksheet(
            TeamProgramParticipation $teamProgramParticipation, string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData): Worksheet
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        return $teamProgramParticipation->submitRootWorksheet($worksheetId, $name, $mission, $formRecordData, $this);
    }

    public function submitBranchWorksheet(
            TeamProgramParticipation $teamProgramParticipation, Worksheet $parentWorksheet, string $branchWorksheetId,
            string $branchWorksheetName, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        return $teamProgramParticipation->submitBranchWorksheet(
                        $parentWorksheet, $branchWorksheetId, $branchWorksheetName, $mission, $formRecordData, $this);
    }

    public function updateWorksheet(
            Worksheet $worksheet, string $worksheetName, FormRecordData $formRecordData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($worksheet);
        $worksheet->update($worksheetName, $formRecordData, $this);
    }

    protected function pullAndRecordEventFromEntity(ContainEvents $entity): void
    {
        foreach ($entity->pullRecordedEvents() as $event) {
            $this->recordEvent(new EventTriggeredByTeamMember($event, $this->id));
        }
    }

    public function submitConsultationRequest(
            TeamProgramParticipation $teamProgramParticipation, string $consultationRequestId,
            ConsultationSetup $consultationSetup, Consultant $consultant,
            ConsultationRequestData $consultationRequestData): ConsultationRequest
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        $consultationRequest = $teamProgramParticipation->submitConsultationRequest(
                $consultationRequestId, $consultationSetup, $consultant, $consultationRequestData, $this);

        $this->pullAndRecordEventFromEntity($consultationRequest);

        return $consultationRequest;
    }

    public function changeConsultationRequestTime(
            TeamProgramParticipation $teamProgramParticipation, string $consultationRequestId,
            ConsultationRequestData $consultationRequestData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        $teamProgramParticipation->changeConsultationRequestTime($consultationRequestId, $consultationRequestData, $this);

        $this->pullAndRecordEventFromEntity($teamProgramParticipation);
    }

    public function cancelConsultationRequest(ConsultationRequest $consultationRequest): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($consultationRequest);
        $consultationRequest->cancel($this);

        $this->pullAndRecordEventFromEntity($consultationRequest);
    }

    public function acceptOfferedConsultationRequest(TeamProgramParticipation $teamProgramParticipation,
            string $consultationRequestId): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        $teamProgramParticipation->acceptOfferedConsultationRequest($consultationRequestId, $this);

        $this->pullAndRecordEventFromEntity($teamProgramParticipation);
    }

    public function submitConsultationSessionReport(
            ConsultationSession $consultationSession, FormRecordData $formRecordData, ?int $mentorRating): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($consultationSession);
        $consultationSession->setParticipantFeedback($formRecordData, $mentorRating, $this);
    }

    public function submitNewCommentInWorksheet(Worksheet $worksheet, string $commentId, string $message): MemberComment
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($worksheet);
        return new MemberComment($this, $worksheet->createComment($commentId, $message, $this));
    }

    public function replyComment(Comment $comment, string $replyId, string $message): MemberComment
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($comment);
        return new MemberComment($this, $comment->createReply($replyId, $message, $this));
    }

    public function logViewLearningMaterialActivity(
            string $logId, Participant $participant, string $learningMaterialId): ViewLearningMaterialActivityLog
    {
        return $participant->logViewLearningMaterialActivity($logId, $learningMaterialId, $this);
    }

    public function submitMetricAssignmentReport(
            TeamProgramParticipation $teamProgramParticipation, string $metricAssignmentReportId,
            DateTimeImmutable $observationTime, MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): MetricAssignmentReport
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        return $teamProgramParticipation->submitMetricAssignmentReport(
                        $metricAssignmentReportId, $observationTime, $metricAssignmentReportDataProvider);
    }

    public function updateMetricAssignmentReport(
            MetricAssignmentReport $metricAssignmentReport,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($metricAssignmentReport);
        $metricAssignmentReport->update($metricAssignmentReportDataProvider);
    }

    public function submitRegistrantProfile(
            TeamProgramRegistration $programRegistration, ProgramsProfileForm $programsProfileForm,
            FormRecordData $formRecordData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($programRegistration);
        $programRegistration->submitProfile($programsProfileForm, $formRecordData);
    }

    public function removeRegistrantProfile(
            TeamProgramRegistration $programRegistration, RegistrantProfile $registrantProfile): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($programRegistration);
        $programRegistration->removeProfile($registrantProfile);
    }

    public function submitParticipantProfile(
            TeamProgramParticipation $teamParticipant, ProgramsProfileForm $programsProfileForm,
            FormRecordData $formRecordData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        $teamParticipant->submitProfile($programsProfileForm, $formRecordData);
    }

    public function removeParticipantProfile(TeamProgramParticipation $teamParticipant,
            ParticipantProfile $participantProfile): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        $teamParticipant->removeProfile($participantProfile);
    }
    
    public function createOKRPeriodInTeamParticipant(
            TeamProgramParticipation $teamParticipant, string $okrPeriodId, OKRPeriodData $okrPeriodData): OKRPeriod
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        return $teamParticipant->createOKRPeriod($okrPeriodId, $okrPeriodData);
    }
    public function updateOKRPeriod(
            TeamProgramParticipation $teamParticipant, OKRPeriod $okrPeriod, OKRPeriodData $okrPeriodData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        $teamParticipant->updateOKRPeriod($okrPeriod, $okrPeriodData);
    }
    public function cancelOKRPeriod(TeamProgramParticipation $teamParticipant, OKRPeriod $okrPeriod): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        $teamParticipant->cancelOKRPeriod($okrPeriod);
    }
    
    public function submitObjectiveProgressReport(
            TeamProgramParticipation $teamParticipant, Objective $objective, string $objectiveProgressReportId, 
            ObjectiveProgressReportData $objectiveProgressReportData): ObjectiveProgressReport
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        return $teamParticipant->submitObjectiveProgressReport(
                $objective, $objectiveProgressReportId, $objectiveProgressReportData);
    }
    public function updateObjectiveProgressReport(
            TeamProgramParticipation $teamParticipant, ObjectiveProgressReport $objectiveProgressReport, 
            ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        $teamParticipant->updateObjectiveProgressReport($objectiveProgressReport, $objectiveProgressReportData);
    }
    public function cancelObjectiveProgressReportSubmission(
            TeamProgramParticipation $teamParticipant, ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamParticipant);
        $teamParticipant->cancelObjectiveProgressReportSubmission($objectiveProgressReport);
    }
    
    public function executeTeamParticipantTask(
            TeamProgramParticipation $teamParticipant, ITaskExecutableByParticipant $task): void
    {
        if (!$this->active) {
            throw RegularException::forbidden('forbidden: only active team member can make this request');
        }
        $teamParticipant->assertBelongsToTeam($this->team);
        $teamParticipant->executeParticipantTask($task);
    }
    
    public function executeParticipantTask(TeamProgramParticipation $teamParticipant, ParticipantTask $task, $payload): void
    {
        if (!$this->active) {
            throw RegularException::forbidden('only active team member can make this request');
        }
        $teamParticipant->assertBelongsToTeam($this->team);
        $teamParticipant->executeTask($task, $payload);
    }

}
