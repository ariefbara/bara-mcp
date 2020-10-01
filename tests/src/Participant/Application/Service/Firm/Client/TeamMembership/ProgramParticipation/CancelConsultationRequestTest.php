<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\ {
    Application\Service\Firm\Client\TeamMembership\TeamProgramParticipationRepository,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\ConsultationRequestRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\ConsultationRequest,
    Domain\Model\TeamProgramParticipation
};
use Tests\TestBase;

class CancelConsultationRequestTest extends TestBase
{

    protected $service;
    protected $consultationRequestRepository, $consultationRequest;
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $programParticipationId = "programParticipationId", $consultationRequestId = "consultationRequestId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestRepository = $this->buildMockOfInterface(ConsultationRequestRepository::class);
        $this->consultationRequestRepository->expects($this->any())
                ->method("ofId")
                ->with($this->consultationRequestId)
                ->willReturn($this->consultationRequest);

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
                ->with($this->programParticipationId)
                ->willReturn($this->teamProgramParticipation);

        $this->service = new CancelConsultationRequest(
                $this->consultationRequestRepository, $this->teamMembershipRepository,
                $this->teamProgramParticipationRepository);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamMembershipId, $this->programParticipationId,
                $this->consultationRequestId);
    }
    public function test_execute_executeTeamMembershipCancelConsultatioNRequestMethod()
    {
        $this->teamMembership->expects($this->once())
                ->method("cancelConsultationRequest")
                ->with($this->teamProgramParticipation, $this->consultationRequest);
        $this->execute();
    }
    public function test_execute_updateConsultationRequestRepository()
    {
        $this->consultationRequestRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
