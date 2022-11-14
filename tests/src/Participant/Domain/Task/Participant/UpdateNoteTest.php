<?php

namespace Participant\Domain\Task\Participant;

use Tests\src\Participant\Domain\Task\Participant\ParticipantTaskTestBase;

class UpdateNoteTest extends ParticipantTaskTestBase
{
    protected $task;
    protected $payload, $content = 'new content';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setupParticipantNoteDependency();
        
        $this->task = new UpdateNote($this->participantNoteRepository);
        //
        $this->payload = new UpdateNotePayload($this->participantNoteId, $this->content);
    }
    
    protected function execute()
    {
        $this->task->execute($this->participant, $this->payload);
    }
    public function test_execute_udpateParticipantNote()
    {
        $this->participantNote->expects($this->once())
                ->method('update')
                ->with($this->content);
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
