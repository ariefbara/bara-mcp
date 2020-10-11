<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

use Participant\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Participant\ConsultationRequestRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\ConsultationRequest
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class CancelConsultationRequestTest extends TestBase
{

    protected $service;
    protected $consultationRequestRepository, $consultationRequest;
    protected $teamMembershipRepository, $teamMembership;
    protected $dispatcher;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $consultationRequestId = "consultationRequestId";

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
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new CancelConsultationRequest(
                $this->consultationRequestRepository, $this->teamMembershipRepository, $this->dispatcher);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamMembershipId, $this->consultationRequestId);
    }
    public function test_execute_executeTeamMembershipCancelConsultatioNRequestMethod()
    {
        $this->teamMembership->expects($this->once())
                ->method("cancelConsultationRequest")
                ->with($this->consultationRequest);
        $this->execute();
    }
    public function test_execute_updateConsultationRequestRepository()
    {
        $this->consultationRequestRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
    public function test_execute_dispatchTeamMembership()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->teamMembership);
        $this->execute();
    }
}
