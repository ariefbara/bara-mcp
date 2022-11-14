<?php

namespace Tests\src\Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ParticipantNote;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class ParticipantTaskTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $participantNoteRepository;
    /**
     * 
     * @var MockObject
     */
    protected $participantNote;
    protected $participantNoteId = 'participantNoteId';
    protected function setupParticipantNoteDependency()
    {
        $this->participantNoteRepository = $this->buildMockOfInterface(ParticipantNoteRepository::class);
        $this->participantNote = $this->buildMockOfClass(ParticipantNote::class);
        $this->participantNoteRepository->expects($this->any())
                ->method('ofId')
                ->with($this->participantNoteId)
                ->willReturn($this->participantNote);
    }
}
