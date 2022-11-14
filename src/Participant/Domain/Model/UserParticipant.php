<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
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
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Participant\Domain\Task\Participant\ParticipantTask;
use Resources\Application\Event\ContainEvents;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class UserParticipant implements ContainEvents
{

    /**
     *
     * @var string
     */
    protected $userId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        ;
    }

    public function quit(): void
    {
        $this->participant->quit();
    }

    public function proposeConsultation(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            ConsultationRequestData $consultationRequestData): ConsultationRequest
    {
        return $this->participant->submitConsultationRequest($consultationRequestId, $consultationSetup, $consultant,
                        $consultationRequestData);
    }

    public function reproposeConsultationRequest(
            string $consultationRequestId, ConsultationRequestData $consulationRequestData): void
    {
        $this->participant->changeConsultationRequestTime($consultationRequestId, $consulationRequestData);
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->participant->acceptOfferedConsultationRequest($consultationRequestId, $consultationSessionId);
    }

    public function createRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        return $this->participant
                        ->createRootWorksheet($worksheetId, $name, $mission, $formRecordData, $teamMember = null);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData): Worksheet
    {
        return $this->participant
                        ->submitBranchWorksheet($parentWorksheet, $worksheetId, $name, $mission, $formRecordData);
    }

    public function replyComment(
            string $commentId, Comment $comment, string $message): Comment
    {
        return $comment->createReply($commentId, $message, $teamMember = null);
    }

    public function pullRecordedEvents(): array
    {
        return $this->participant->pullRecordedEvents();
    }

    public function ownAllAttachedFileInfo(MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): bool
    {
        $ownedAllFileInfo = true;
        foreach ($metricAssignmentReportDataProvider->iterateAllAttachedFileInfo() as $fileInfo) {
            $ownedAllFileInfo = $ownedAllFileInfo && $fileInfo->belongsToUser($this->userId);
        }
        return $ownedAllFileInfo;
    }

    public function submitMetricAssignmentReport(
            string $metricAssignmentReportId, DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): MetricAssignmentReport
    {
        return $this->participant->submitMetricAssignmentReport(
                        $metricAssignmentReportId, $observationTime, $metricAssignmentReportDataProvider);
    }
    
    public function submitProfile(ProgramsProfileForm $programProfileForm, FormRecordData $formRecordData): void
    {
        $this->participant->submitProfile($programProfileForm, $formRecordData);
    }
    
    public function removeProfile(ParticipantProfile $profile): void
    {
        $this->participant->removeProfile($profile);
    }
    
    public function createOKRPeriod(string $okrPeriodId, OKRPeriodData $okrPeriodData): OKRPeriod
    {
        return $this->participant->createOKRPeriod($okrPeriodId, $okrPeriodData);
    }
    public function updateOKRPeriod(OKRPeriod $okrPeriod, OKRPeriodData $okrPeriodData): void
    {
        $this->participant->updateOKRPeriod($okrPeriod, $okrPeriodData);
    }
    public function cancelOKRPeriod(OKRPeriod $okrPeriod): void
    {
        $this->participant->cancelOKRPeriod($okrPeriod);
    }
    
    public function submitObjectiveProgressReport(
            Objective $objective, string $objectiveProgressReportId, 
            ObjectiveProgressReportData $objectiveProgressReportData): ObjectiveProgressReport
    {
        return $this->participant->submitObjectiveProgressReport(
                $objective, $objectiveProgressReportId, $objectiveProgressReportData);
    }
    public function updateObjectiveProgressReport(
            ObjectiveProgressReport $objectiveProgressReport, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        $this->participant->updateObjectiveProgressReport($objectiveProgressReport, $objectiveProgressReportData);
    }
    public function cancelObjectiveProgressReportSubmission(ObjectiveProgressReport $objectiveProgressReport): void
    {
        $this->participant->cancelObjectiveProgressReportSubmission($objectiveProgressReport);
    }
    
    public function executeTask(ParticipantTask $task, $payload): void
    {
        $this->participant->executeTask($task, $payload);
    }

}
