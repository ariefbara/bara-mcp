<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
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

}
