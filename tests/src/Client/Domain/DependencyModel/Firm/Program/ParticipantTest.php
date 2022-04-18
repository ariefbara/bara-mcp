<?php

namespace Client\Domain\DependencyModel\Firm\Program;

use Client\Domain\DependencyModel\Firm\Program;
use Tests\TestBase;

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
    
    protected function isActiveParticipationCorrespondWithProgram()
    {
        return $this->participant->isActiveParticipationCorrespondWithProgram($this->program);
    }
    public function test_isActiveParticipationCorrespondWithProgram_activeParticipantOfSameProgram_returnTrue()
    {
        $this->assertTrue($this->isActiveParticipationCorrespondWithProgram());
    }
    public function test_isActiveParticipationCorrespondWithProgram_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->isActiveParticipationCorrespondWithProgram());
    }
    public function test_isActiveParticipationCorrespondWithProgram_differentProgram_returnFalse()
    {
        $this->participant->program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->isActiveParticipationCorrespondWithProgram());
    }
}

class TestableParticipant extends Participant
{
    public $program;
    public $id = 'id';
    public $active = 'true';
    
    public function __construct()
    {
    }
}
