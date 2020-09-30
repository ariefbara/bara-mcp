<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\TeamProgramParticipation
};
use Tests\TestBase;

class QuitProgramParticipationTest extends TestBase
{
    protected $service;
    protected $teamMembershipRepository, $teamMembership;
    protected $teamProgramParticipationRepository, $teamProgramParticipation;
    
    protected $firmId = "firmid", $clientId = "clientId", $teamMembershipId = 'teammembershipId', $teamProgramParticipationId = "teamProgramParticiaptioNId";
    
    protected function setUp(): void
    {
        parent::setUp();
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
        
        $this->service = new QuitProgramParticipation($this->teamMembershipRepository, $this->teamProgramParticipationRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamMembershipId, $this->teamProgramParticipationId);
    }
    public function test_execute_executeTeamMembershipQuitProgramParticipation()
    {
        $this->teamMembership->expects($this->once())
                ->method("quitTeamProgramParticipation")
                ->with($this->teamProgramParticipation);
        $this->execute();
    }
    public function test_execute_updateTeamProgramParticipationRepository()
    {
        $this->teamProgramParticipationRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
