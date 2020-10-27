<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership;

use Participant\ {
    Application\Service\Firm\Client\TeamMembershipRepository,
    Application\Service\Firm\ProgramRepository,
    Application\Service\TeamProgramRegistrationRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program
};
use Tests\TestBase;

class RegisterTeamToProgramTest extends TestBase
{
    protected $service;
    protected $teamProgramRegistrationRepository, $nextId = "nextId";
    protected $teamMembershipRepository, $teamMembership;
    protected $programRepository, $program;
    
    protected $firmId = "firmId", $clientId = "clientId", $teamMembershipId = "teamMembershipId", $programId = "programId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramRegistrationRepository = $this->buildMockOfInterface(TeamProgramRegistrationRepository::class);
        $this->teamProgramRegistrationRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->teamMembership = $this->buildMockOfClass(TeamMembership::class);
        $this->teamMembershipRepository = $this->buildMockOfInterface(TeamMembershipRepository::class);
        $this->teamMembershipRepository->expects($this->any())
                ->method("aTeamMembershipCorrespondWithTeam")
                ->with($this->firmId, $this->clientId, $this->teamMembershipId)
                ->willReturn($this->teamMembership);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);
        
        $this->service = new RegisterTeamToProgram($this->teamProgramRegistrationRepository, $this->teamMembershipRepository, $this->programRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->teamMembershipId, $this->programId);
    }
    public function test_execute_addTeamProgramRegistrationToRepository()
    {
        $this->teamMembership->expects($this->once())
                ->method("registerTeamToProgram")
                ->with($this->nextId, $this->program);
        $this->teamProgramRegistrationRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
