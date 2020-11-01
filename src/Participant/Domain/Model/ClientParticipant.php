<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\MetricAssignment\MetricAssignmentReport,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment,
    Service\MetricAssignmentReportDataProvider
};
use Resources\ {
    Application\Event\ContainEvents,
    Exception\RegularException,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ClientParticipant implements ContainEvents
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
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        
    }

    public function quit(): void
    {
        $this->participant->quit();
    }

    public function proposeConsultation(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime): ConsultationRequest
    {
        return $this->participant->submitConsultationRequest(
                        $consultationRequestId, $consultationSetup, $consultant, $startTime);
    }

    public function reproposeConsultationRequest(string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $this->participant->changeConsultationRequestTime($consultationRequestId, $startTime);
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->participant->acceptOfferedConsultationRequest($consultationRequestId, $consultationSessionId);
    }

    public function createRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        return $this->participant->createRootWorksheet($worksheetId, $name, $mission, $formRecordData,
                        $teamMember = null);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $branchId, string $name, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        return $this->participant->submitBranchWorksheet($parentWorksheet, $branchId, $name, $mission, $formRecordData);
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

    public function submitMetricAssignmentReport(
            string $metricAssignmentReportId, ?DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): MetricAssignmentReport
    {
        $this->assertAllAttachedFileBelongsToClient($metricAssignmentReportDataProvider);
        return $this->participant->submitMetricAssignmentReport(
                        $metricAssignmentReportId, $observationTime, $metricAssignmentReportDataProvider);
    }
    
    public function ownAllAttachedFileInfo(MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): bool
    {
        $allFileInfoBelongsToClient = true;
        foreach ($metricAssignmentReportDataProvider->iterateAllAttachedFileInfo() as $fileInfo) {
            $allFileInfoBelongsToClient = $allFileInfoBelongsToClient && $fileInfo->belongsToClient($this->client);
        }
        return $allFileInfoBelongsToClient;
    }

    protected function assertAllAttachedFileBelongsToClient(
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        foreach ($metricAssignmentReportDataProvider->iterateAllAttachedFileInfo() as $fileInfo) {
            if (!$fileInfo->belongsToClient($this->client)) {
                $errorDetail = "forbidden: unable to attach file not owned";
                throw RegularException::forbidden($errorDetail);
            }
        }
    }

}
