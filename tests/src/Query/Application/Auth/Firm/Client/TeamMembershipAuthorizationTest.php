<?php

namespace Query\Application\Auth\Firm\Client;

use Tests\TestBase;

class TeamMembershipAuthorizationTest extends TestBase
{
    protected $authZ;
    protected $teamMembershipRepository;
    
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembershipRepository = $this->buildMockOfClass(TeamMembershipRepository::class);
        $this->authZ = new TeamMembershipAuthorization($this->teamMembershipRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->clientId, $this->teamMembershipId);
    }
    public function test_execute_containRecordOfActiveTeamMembershipInRepository_void()
    {
        $this->teamMembershipRepository->expects($this->once())
                ->method("containRecordOfActiveTeamMembership")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn(true);
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfActiveTeamMembershipInRepository_forbiddenError()
    {
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
