<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\ {
    Program,
    Team
};
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class TeamProgramRegistrationTest extends TestBase
{
    protected $teamProgramRegistration;
    protected $team;
    protected $program;
    
    protected $programRegistration;
    
    protected $id = "newTeamProgramRegistrationId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->program = $this->buildMockOfClass(Program::class);
        $this->program->expects($this->any())
                ->method("isRegistrationOpenFor")
                ->willReturn(true);
        
        $this->teamProgramRegistration = new TestableTeamProgramRegistration($this->team, "id", $this->program);
        $this->programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->teamProgramRegistration->programRegistration = $this->programRegistration;
    }
    
    protected function executeConstruct()
    {
        return new TestableTeamProgramRegistration($this->team, $this->id, $this->program);
    }
    public function test_construct_setProperties()
    {
        $teamProgramRegistration = $this->executeConstruct();
        $this->assertEquals($this->team, $teamProgramRegistration->team);
        $this->assertEquals($this->id, $teamProgramRegistration->id);
        
        $programRegistration = new ProgramRegistration($this->program, $this->id);
        $this->assertEquals($programRegistration, $teamProgramRegistration->programRegistration);
    }
    public function test_construct_programNotOpenForTeamParticipantType_forbiddenError()
    {
        $program = $this->buildMockOfClass(Program::class);
        $program->expects($this->once())
                ->method("isRegistrationOpenFor")
                ->with(ParticipantTypes::TEAM_TYPE)
                ->willReturn(false);
        $operation = function () use ($program) {
            new TestableTeamProgramRegistration($this->team, $this->id, $program);
        };
        $errorDetail = "forbidden: program registration is closed or unavailable for team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_cancel_cancelProgramRegistration()
    {
        $this->programRegistration->expects($this->once())
                ->method("cancel");
        $this->teamProgramRegistration->cancel();
    }
    
    public function test_teamEquals_sameTeam_returnTrue()
    {
        $this->assertTrue($this->teamProgramRegistration->teamEquals($this->team));
    }
    public function test_teamEquals_differentTeam_returnFalse()
    {
        $team = $this->buildMockOfClass(Team::class);
        $this->assertFalse($this->teamProgramRegistration->teamEquals($team));
    }
    
    public function test_isUnconcludedRegistrationToProgram_returnResultOfProgramRegistrationsIsUnconcludedRegistrationToProgramMethod()
    {
        $this->programRegistration->expects($this->once())
                ->method("isUnconcludedRegistrationToProgram")
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->teamProgramRegistration->isUnconcludedRegistrationToProgram($this->program));
    }
}

class TestableTeamProgramRegistration extends TeamProgramRegistration
{
    public $team;
    public $id;
    public $programRegistration;
}
