<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ProgramInterface;
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    
    protected $program, $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->participant->active = true;
        $this->participant->programId = $this->programId;
        
        $this->program = $this->buildMockOfInterface(ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
    }
    
    protected function executeIsActiveParticipantOfProgram()
    {
        return $this->participant->isActiveParticipantOfProgram($this->program);
    }
    public function test_isActiveParticipantOfProgram_sameProgramId_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveParticipantOfProgram());
    }
    public function test_isActiveParticipantOfProgram_differentProgramId_returnFalse()
    {
        $this->participant->programId = 'differentId';
        $this->assertFalse($this->executeIsActiveParticipantOfProgram());
    }
    public function test_isActiveParticipantOfProgram_inactiveParticipation_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->executeIsActiveParticipantOfProgram());
    }
}

class TestableParticipant extends Participant
{
    public $programId;
    public $id;
    public $active;
    
    function __construct()
    {
        parent::__construct();
    }
}
