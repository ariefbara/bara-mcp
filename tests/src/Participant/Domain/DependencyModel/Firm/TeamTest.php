<?php

namespace Participant\Domain\DependencyModel\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\ {
    TeamProgramParticipation,
    TeamProgramRegistration
};
use Tests\TestBase;

class TeamTest extends TestBase
{
    protected $team;
    protected $teamProgramParticipation;
    protected $teamProgramRegistration;

    protected $teamProgramRegistrationId = "teamProgramRegistrationId", $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->team = new TestableTeam();
        $this->team->teamProgramParticipations = new ArrayCollection();
        $this->team->teamProgramRegistrations = new ArrayCollection();
        
        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->team->teamProgramParticipations->add($this->teamProgramParticipation);
        
        $this->teamProgramRegistration = $this->buildMockOfClass(TeamProgramRegistration::class);
        $this->team->teamProgramRegistrations->add($this->teamProgramRegistration);
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    protected function executeRegisterToProgram()
    {
        $this->program->expects($this->any())
                ->method('firmIdEquals')
                ->willReturn(true);
        $this->program->expects($this->any())
                ->method('isRegistrationOpenFor')
                ->willReturn(true);
        return $this->team->registerToProgram($this->teamProgramRegistrationId, $this->program);
    }
    public function test_executeRegisterToProgram_returnProgramRegistration()
    {
        $this->program->expects($this->any())
                ->method('firmIdEquals')
                ->willReturn(true);
        $this->program->expects($this->any())
                ->method('isRegistrationOpenFor')
                ->willReturn(true);
        $teamProgramRegistration = new TeamProgramRegistration($this->team, $this->teamProgramRegistrationId, $this->program);
        $this->assertEquals($teamProgramRegistration, $this->executeRegisterToProgram());
    }
    public function test_registerToProgram_programFromDifferentFirm_forbiddenError()
    {
        $this->program->expects($this->once())
                ->method('firmIdEquals')
                ->with($this->team->firmId)
                ->willReturn(false);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: cannot register to program from different firm';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_haveUnconcludedRegistrationToSameProgram_forbiddenError()
    {
        $this->teamProgramRegistration->expects($this->once())
                ->method('isUnconcludedRegistrationToProgram')
                ->with($this->program)
                ->willReturn(true);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: your team already registered to this program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_registerToProgram_alreadyActiveParticipantOfSameProgram_forbiddenError()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('isActiveParticipantOfProgram')
                ->with($this->program)
                ->willReturn(true);
        $operation = function (){
            $this->executeRegisterToProgram();
        };
        $errorDetail = 'forbidden: your team already participante in this program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
}

class TestableTeam extends Team
{
    public $firmId = "firmId";
    public $id;
    public $teamProgramParticipations;
    public $teamProgramRegistrations;
    
    function __construct()
    {
        
    }
}
