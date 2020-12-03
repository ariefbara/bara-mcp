<?php

namespace User\Domain\DependencyModel\Firm\Program;

use Tests\TestBase;
use User\Domain\DependencyModel\Firm\Program;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant->program = $this->program;
    }
    
    public function test_isActiveParticipantOfProgram_activeProgramParticipant_returnTrue()
    {
        $this->assertTrue($this->participant->isActiveParticipantOfProgram($this->participant->program));
    }
    public function test_isActiveParticipantOfProgram_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->participant->isActiveParticipantOfProgram($this->participant->program));
    }
    public function test_isActiveParticipantOfProgram_differenetProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->participant->isActiveParticipantOfProgram($program));
    }
}

class TestableParticipant extends Participant
{
    public $program;
    public $id;
    public $enrolledTime;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
