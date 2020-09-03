<?php

namespace User\Domain\Model\User;

use Tests\TestBase;
use User\Domain\Model\ProgramInterface;

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
    
    protected function executeIsActiveParticipantInProgram()
    {
        return $this->participant->isActiveParticipantInProgram($this->program);
    }
    public function test_isActiveParticipantInProgram_activeParticipantOfSameProgram_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveParticipantInProgram());
    }
    public function test_isActiveParticipantInProgram_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->executeIsActiveParticipantInProgram());
    }
    public function test_isActiveParticipantInProgram_activeParticipantOfDifferentProgram_returnFalse()
    {
        $this->participant->programId = 'different';
        $this->assertFalse($this->executeIsActiveParticipantInProgram());
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
