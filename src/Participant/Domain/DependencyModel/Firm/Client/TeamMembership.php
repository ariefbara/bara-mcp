<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Event\EventTriggeredByTeamMember,
    Model\Participant,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\MetricAssignment,
    Model\Participant\MetricAssignment\MetricAssignmentReport,
    Model\Participant\MetricAssignment\MetricAssignmentReportData,
    Model\Participant\ViewLearningMaterialActivityLog,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment,
    Model\TeamProgramParticipation,
    Model\TeamProgramRegistration
};
use Resources\ {
    Application\Event\ContainEvents,
    Domain\Model\EntityContainEvents,
    Exception\RegularException
};
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

    public function registerTeamToProgram(string $teamProgramRegistrationId, Program $program): TeamProgramRegistration
    {
        $this->assertActive();
        return $this->team->registerToProgram($teamProgramRegistrationId, $program);
    }

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
            ConsultationSetup $consultationSetup, Consultant $consultant, DateTimeImmutable $startTime): ConsultationRequest
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        $consultationRequest = $teamProgramParticipation->submitConsultationRequest(
                $consultationRequestId, $consultationSetup, $consultant, $startTime, $this);

        $this->pullAndRecordEventFromEntity($consultationRequest);

        return $consultationRequest;
    }

    public function changeConsultationRequestTime(
            TeamProgramParticipation $teamProgramParticipation, string $consultationRequestId,
            DateTimeImmutable $startTime): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($teamProgramParticipation);
        $teamProgramParticipation->changeConsultationRequestTime($consultationRequestId, $startTime, $this);

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
            ConsultationSession $consultationSession, FormRecordData $formRecordData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($consultationSession);
        $consultationSession->setParticipantFeedback($formRecordData, $this);
    }

    public function submitNewCommentInWorksheet(Worksheet $worksheet, string $commentId, string $message): Comment
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($worksheet);
        return $worksheet->createComment($commentId, $message, $this);
    }

    public function replyComment(Comment $comment, string $replyId, string $message): Comment
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($comment);
        return $comment->createReply($replyId, $message, $this);
    }

    public function logViewLearningMaterialActivity(
            string $logId, Participant $participant, string $learningMaterialId): ViewLearningMaterialActivityLog
    {
        return $participant->logViewLearningMaterialActivity($logId, $learningMaterialId, $this);
    }

    public function submitReportInMetricAssignment(
            MetricAssignment $metricAssignment, string $metricAssignmentReportId, DateTimeImmutable $observeTime, 
            MetricAssignmentReportData $metricAssignmentReportData): MetricAssignmentReport
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($metricAssignment);
        return $metricAssignment->submitReport($metricAssignmentReportId, $observeTime, $metricAssignmentReportData);
    }

    public function updateMetricAssignmentReport(
            MetricAssignmentReport $metricAssignmentReport, MetricAssignmentReportData $metricAssignmentReportData): void
    {
        $this->assertActive();
        $this->assertAssetBelongsToTeam($metricAssignmentReport);
        $metricAssignmentReport->update($metricAssignmentReportData);
    }

}
