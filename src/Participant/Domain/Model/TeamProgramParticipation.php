<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Client\AssetBelongsToTeamInterface;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Resources\Application\Event\ContainEvents;
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

}
