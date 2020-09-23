<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\TeamMembership;
use Tests\TestBase;

class QuitTeamMembershipTest extends TestBase
{
    protected $service;
    protected $teamMembership, $teamMembershipRepository;
    
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);
        
        $this->service = new QuitTeamMembership($this->teamMembershipRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamMembershipId);
    }
    public function test_execute_quitTeamMembership()
    {
        $this->teamMembership->expects($this->once())
                ->method("quit");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMembershipRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
