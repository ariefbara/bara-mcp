<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\TeamProgramRegistrationRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\TeamProgramRegistration
};
use Tests\TestBase;

class CancelTeamProgramRegistrationTest extends TestBase
{

    protected $service;
    protected $teamProgramRegistrationRepository, $teamProgramRegistration;
    protected $teamMembershipRepository, $teamMembership;
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId",
            $teamProgramRegistrationId = "teamProgramRegistrationId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramRegistration = $this->buildMockOfClass(TeamProgramRegistration::class);
        $this->teamProgramRegistrationRepository = $this->buildMockOfInterface(TeamProgramRegistrationRepository::class);
        $this->teamProgramRegistrationRepository->expects($this->any())
                ->method("ofId")
                ->with($this->teamProgramRegistrationId)
                ->willReturn($this->teamProgramRegistration);

        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);

        $this->service = new CancelTeamProgramRegistration(
                $this->teamProgramRegistrationRepository, $this->teamMembershipRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamMembershipId, $this->teamProgramRegistrationId);
    }
    public function test_execute_executeTeamMembershipsCancelTeamProgramRegistration()
    {
        $this->teamMembership->expects($this->once())
                ->method("cancelTeamProgramRegistration")
                ->with($this->teamProgramRegistration);
        $this->execute();
    }
    public function test_execute_udpateTeamProgramRegistrationRepository()
    {
        $this->teamProgramRegistrationRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
