<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Query\Domain\Model\Firm\ParticipantTypes;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class TeamProgramRegistrationTest extends TestBase
{
    protected $teamProgramRegistration;
    protected $team;
    protected $program;
    
    protected $programRegistration;
    
    protected $id = "newTeamProgramRegistrationId";
    protected $programsProfileForm, $formRecordData;
    protected $registrantProfile;

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
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->registrantProfile = $this->buildMockOfClass(RegistrantProfile::class);
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
    
    public function test_isUnconcludedRegistrationToProgram_returnResultOfProgramRegistrationsIsUnconcludedRegistrationToProgramMethod()
    {
        $this->programRegistration->expects($this->once())
                ->method("isUnconcludedRegistrationToProgram")
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->teamProgramRegistration->isUnconcludedRegistrationToProgram($this->program));
    }
    
    public function test_belongsToTeam_sameTeam_returnTrue()
    {
        $this->assertTrue($this->teamProgramRegistration->belongsToTeam($this->teamProgramRegistration->team));
    }
    public function test_belongsToTeam_differentTeam_returnFalse()
    {
        $team = $this->buildMockOfClass(Team::class);
        $this->assertFalse($this->teamProgramRegistration->belongsToTeam($team));
    }
    
    public function test_submitProfile_submitRegistrantProfile()
    {
        $this->programRegistration->expects($this->once())
                ->method("submitProfile")
                ->with($this->programsProfileForm, $this->formRecordData);
        $this->teamProgramRegistration->submitProfile($this->programsProfileForm, $this->formRecordData);
    }
    
    public function test_removeProfile_removeRegistrantProfile()
    {
        $this->programRegistration->expects($this->once())
                ->method("removeProfile")
                ->with($this->registrantProfile);
        $this->teamProgramRegistration->removeProfile($this->registrantProfile);
    }
}

class TestableTeamProgramRegistration extends TeamProgramRegistration
{
    public $team;
    public $id;
    public $programRegistration;
}
