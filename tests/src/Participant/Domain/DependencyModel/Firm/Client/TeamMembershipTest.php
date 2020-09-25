<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Team,
    Model\TeamProgramRegistration
};
use Tests\TestBase;

class TeamMembershipTest extends TestBase
{
    protected $teamMembership;
    protected $team;
    
    protected $teamProgramRegistrationId = "programRegistrationId", $program;
    protected $teamProgramRegistration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = new TestableTeamMembership();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamMembership->team = $this->team;
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->teamProgramRegistration = $this->buildMockOfClass(TeamProgramRegistration::class);
    }
    
    protected function executeRegisterTeamToProgram()
    {
        return $this->teamMembership->registerTeamToProgram($this->teamProgramRegistrationId, $this->program);
    }
    public function test_registerTeamToProgram_returnTeamsRegisterToProgramResult()
    {
        $this->team->expects($this->once())
                ->method("registerToProgram")
                ->with($this->teamProgramRegistrationId, $this->program)
                ->willReturn($this->teamProgramRegistration);
        $this->assertEquals($this->teamProgramRegistration, $this->executeRegisterTeamToProgram());
    }
    public function test_registerTeamToProgram_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeRegisterTeamToProgram();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeCancelTeamProgramRegistration()
    {
        $this->teamProgramRegistration->expects($this->any())
                ->method("teamEquals")
                ->willReturn(true);
        $this->teamMembership->cancelTeamprogramRegistration($this->teamProgramRegistration);
    }
    public function test_cancelTeamProgramRegistration_executeTeamProgramRegistrationsCancelMethod()
    {
        $this->teamProgramRegistration->expects($this->once())
                ->method("cancel");
        $this->executeCancelTeamProgramRegistration();
    }
    public function test_cancel_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeCancelTeamProgramRegistration();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_cancel_teamProgramRegistrationOfDifferentTeam_forbiddenError()
    {
        $this->teamProgramRegistration->expects($this->once())
                ->method("teamEquals")
                ->with($this->team)
                ->willReturn(false);
        $operation = function (){
            $this->executeCancelTeamProgramRegistration();
        };
        $errorDetail = "forbidden: unable to alter registration from other team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
}

class TestableTeamMembership extends TeamMembership
{
    public $client;
    public $id;
    public $team;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
