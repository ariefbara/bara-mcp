<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class SubmitNoteTest extends ParticipantTaskTestBase
{
    protected $task;
    protected $payload, $labelData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupParticipantNoteDependency();
        
        $this->labelData = new LabelData('name', 'description');
        
        $this->task = new SubmitNote($this->participantNoteRepository);
        $this->payload = new SubmitNotePayload($this->labelData);
    }
    
    protected function execute()
    {
        $this->participantNoteRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->participantNoteId);
        $this->task->execute($this->participant, $this->payload);
    }
    public function test_execute_addNoteSubmittedByParticipantToRepository()
    {
        $this->participant->expects($this->once())
                ->method('submitNote')
                ->with($this->participantNoteId, $this->labelData)
                ->willReturn($this->participantNote);
        
        $this->participantNoteRepository->expects($this->once())
                ->method('add')
                ->with($this->participantNote);
        
        $this->execute();
    }
    public function test_execute_setSubmittedNoteInPayload()
    {
        $this->execute();
        $this->assertSame($this->participantNoteId, $this->payload->submittedNoteId);
    }
    
}
