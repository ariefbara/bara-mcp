<?php

namespace Tests\src\Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TaskInProgramTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $participantRepository;
    /**
     * 
     * @var MockObject
     */
    protected $participant;
    protected $participantId = 'participantId';
    protected function prepareParticipantDependency()
    {
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantId)
                ->willReturn($this->participant);
    }
}
