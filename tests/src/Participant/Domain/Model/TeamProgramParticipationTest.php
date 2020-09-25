<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\Program;
use Tests\TestBase;

class TeamProgramParticipationTest extends TestBase
{
    protected $teamProgramParticipation;
    protected $programParticipation;
    
    protected $program;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamProgramParticipation = new TestableTeamProgramParticipation();
        $this->programParticipation = $this->buildMockOfClass(Participant::class);
        $this->teamProgramParticipation->programParticipation = $this->programParticipation;
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_isActiveParticipantOfProgram_returnProgramParticipationsIsActiveParticipantOfProgramResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("isActiveParticipantOfProgram")
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->teamProgramParticipation->isActiveParticipantOfProgram($this->program));
    }
}

class TestableTeamProgramParticipation extends TeamProgramParticipation
{
    public $team;
    public $id;
    public $programParticipation;
    
    function __construct()
    {
        parent::__construct();
    }
}
