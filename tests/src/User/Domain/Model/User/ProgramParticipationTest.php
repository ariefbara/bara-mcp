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
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->programParticipation->participant = $this->participant;
        
        $this->program = $this->buildMockOfInterface(ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
    }
    
    public function test_isActiveParticipantInProgram_returnParticipantIsActiveParticipantInProgramResult()
    {
        $this->participant->expects($this->once())
                ->method('isActiveParticipantInProgram')
                ->with($this->program);
        $this->programParticipation->isActiveParticipantInProgram($this->program);
        
    }
}

class TestableProgramParticipation extends ProgramParticipation
{
    public $user;
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
