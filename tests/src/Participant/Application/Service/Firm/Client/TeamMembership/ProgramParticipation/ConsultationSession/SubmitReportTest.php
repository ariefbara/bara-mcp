<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ConsultationSession;

use Participant\{
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\ConsultationSessionRepository,
    Application\Service\UserParticipant\MissionRepository,
    Application\Service\UserParticipant\SubmitBranchWorksheet,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\Participant\ConsultationSession,
    Domain\Model\TeamProgramParticipation
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitReportTest extends TestBase
{

    protected $service;
    protected $consultationSessionRepository, $consultationSession;
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $teamProgramParticipationId = "teamProgramParticipationId", $consultationSessionId = "consultationSessionId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionRepository = $this->buildMockOfInterface(ConsultationSessionRepository::class);
        $this->consultationSessionRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationSessionId)
                ->willReturn($this->consultationSession);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamProgramParticipationRepository = $this->buildMockOfInterface(TeamProgramParticipationRepository::class);
        $this->teamProgramParticipationRepository->expects($this->any())
                ->method("ofId")
                ->with($this->teamProgramParticipationId)
                ->willReturn($this->teamProgramParticipation);

        $this->service = new SubmitReport(
                $this->consultationSessionRepository, $this->teamMembershipRepository,
                $this->teamProgramParticipationRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamMembershipId, $this->teamProgramParticipationId,
                $this->consultationSessionId, $this->formRecordData);
    }
    public function test_execute_executeTeamMembershipSubmitConsultationReportMethod()
    {
        $this->teamMembership->expects($this->once())
                ->method("submitConsultationSessionReport")
                ->with($this->teamProgramParticipation, $this->consultationSession, $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateConsultationSessionRepository()
    {
        $this->consultationSessionRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
