<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ProgramInterface;
use Tests\TestBase;

class ProgramParticipationTest extends TestBase
{

    protected $programParticipation;
    protected $participant;
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

    public function test_isActiveParticipantOfProgram_returnParticipantIsActiveParticipantOfProgramResult()
    {
        $this->participant->expects($this->once())
                ->method('isActiveParticipantOfProgram')
                ->with($this->program);
        $this->programParticipation->isActiveParticipantOfProgram($this->program);
    }

}

class TestableProgramParticipation extends ProgramParticipation
{

    public $client;
    public $id;
    public $programId;
    public $participant;

    function __construct()
    {
        parent::__construct();
    }

}
