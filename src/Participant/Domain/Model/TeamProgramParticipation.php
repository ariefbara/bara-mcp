<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\{
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet
};
use Resources\{
    Application\Event\ContainEvents,
    Uuid
};
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

}
