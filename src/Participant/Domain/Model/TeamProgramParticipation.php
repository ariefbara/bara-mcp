<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\Worksheet
};
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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

    protected function __construct()
    {
        
    }

    public function teamEquals(Team $team): bool
    {
        return $this->team === $team;
    }

    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->programParticipation->isActiveParticipantOfProgram($program);
    }

    public function submitRootWorksheet(
            string $worksheetId, string $name, Mission $mission, FormRecordData $formRecordData): Worksheet
    {
        return $this->programParticipation->createRootWorksheet($worksheetId, $name, $mission, $formRecordData);
    }

    public function submitBranchWorksheet(
            Worksheet $parentWorksheet, string $worksheetId, string $name, Mission $mission,
            FormRecordData $formRecordData): Worksheet
    {
        return $this->programParticipation
                        ->submitBranchWorksheet($parentWorksheet, $worksheetId, $name, $mission, $formRecordData);
    }

    public function updateWorksheet(Worksheet $worksheet, string $name, FormRecordData $formRecordData): void
    {
        $this->programParticipation->updateWorksheet($worksheet, $name, $formRecordData);
    }

    public function quit(): void
    {
        $this->programParticipation->quit();
    }

    public function submitConsultationRequest(
            string $consultationRequestId, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime, TeamMembership $teamMember): ConsultationRequest
    {
        return $this->programParticipation->submitConsultationRequest(
                        $consultationRequestId, $consultationSetup, $consultant, $startTime, $teamMember);
    }

    public function changeConsultationRequestTime(
            string $consultationRequestId, DateTimeImmutable $startTime, TeamMembership $teamMember): void
    {
        $this->programParticipation->changeConsultationRequestTime($consultationRequestId, $startTime, $teamMember);
    }

    public function cancelConsultationRequest(ConsultationRequest $consultationRequest, TeamMembership $teamMember): void
    {
        $this->programParticipation->cancelConsultationRequest($consultationRequest, $teamMember);
    }

    public function acceptOfferedConsultationRequest(string $consultationRequestId, TeamMembership $teamMember): void
    {
        $consultationSessionId = Uuid::generateUuid4();
        $this->programParticipation->acceptOfferedConsultationRequest(
                $consultationRequestId, $consultationSessionId, $teamMember);
    }

    public function submitConsultationSessionReport(
            ConsultationSession $consultationSession, FormRecordData $formRecordData): void
    {
        $this->programParticipation->submitConsultationSessionReport($consultationSession, $formRecordData);
    }

}
