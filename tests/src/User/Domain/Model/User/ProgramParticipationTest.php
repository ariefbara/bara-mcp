<?php

namespace User\Domain\Model\User;

use Tests\TestBase;
use User\Domain\Model\ProgramInterface;

class ProgramParticipationTest extends TestBase
{
    protected $programParticipation, $participant;
    
    protected $program, $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = new TestableProgramParticipation();
        $this->programParticipation->programId = $this->programId;
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->programParticipation->participant = $this->participant;
        
        $this->program = $this->buildMockOfInterface(ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
    }
    
    protected function executeIsActiveParticipantInProgram()
    {
        $this->participant->expects($this->any())
                ->method('isActive')
                ->willReturn(true);
        return $this->programParticipation->isActiveParticipantInProgram($this->program);
    }
    public function test_isActiveParticipantInProgram_sameProgramId_returnTrue()
    {
        $this->assertTrue($this->executeIsActiveParticipantInProgram());
    }
    public function test_isActiveParticipantInProgram_differentProgramId_returnFalse()
    {
        $this->programParticipation->programId = 'differentId';
        $this->assertFalse($this->executeIsActiveParticipantInProgram());
    }
    public function test_isActiveParticipantInProgram_inactiveParticipation_returnFalse()
    {
        $this->participant->expects($this->once())
                ->method('isActive')
                ->willReturn(false);
        $this->assertFalse($this->executeIsActiveParticipantInProgram());
    }
}

class TestableProgramParticipation extends ProgramParticipation
{
    public $user;
    public $id;
    public $programId;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
