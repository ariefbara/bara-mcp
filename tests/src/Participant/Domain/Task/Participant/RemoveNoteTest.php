<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class RemoveNoteTest extends ParticipantTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupParticipantNoteDependency();
        
        $this->task = new RemoveNote($this->participantNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant, $this->participantNoteId);
    }
    public function test_execute_removeNote()
    {
        $this->participantNote->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_assertNoteManageableByParticipant()
    {
        $this->participantNote->expects($this->once())
                ->method('assertManageableByParticipant')
                ->with($this->participant);
        $this->execute();
    }
}
