<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class UpdateNoteTest extends ParticipantTaskTestBase
{
    protected $task;
    protected $payload, $labelData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupParticipantNoteDependency();
        
        $this->labelData = new LabelData('name', 'description');
        
        $this->task = new UpdateNote($this->participantNoteRepository);
        //
        $this->payload = new UpdateNotePayload($this->participantNoteId, $this->labelData);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant, $this->payload);
    }
    public function test_execute_udpateParticipantNote()
    {
        $this->participantNote->expects($this->once())
                ->method('update')
                ->with($this->labelData);
        $this->execute();
    }
    public function test_execute_assertParticipantNoteManageableByParticipant()
    {
        $this->participantNote->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
