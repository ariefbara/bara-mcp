<?php

namespace Personnel\Domain\Task\Mentor;

use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class SubmitNoteTest extends MentorTaskTestBase
{
    protected $task;
    protected $payload, $content = 'content string', $viewableByParticipant = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setParticipantRelatedTask();
        $this->setConsultantNoteRelatedTask();
        
        $this->task = new SubmitNote($this->consultantNoteRepository, $this->participantRepository);
        
        $this->payload = new SubmitNotePayload($this->participantId, $this->content, $this->viewableByParticipant);
    }
    
    protected function execute()
    {
        $this->consultantNoteRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->consultantNoteId);
        $this->task->execute($this->mentor, $this->payload);
    }
    public function test_execute_addConsultantNoteToRepository()
    {
        $this->mentor->expects($this->once())
                ->method('submitNote')
                ->with($this->consultantNoteId, $this->participant, $this->content, $this->viewableByParticipant)
                ->willReturn($this->consultantNote);
        $this->consultantNoteRepository->expects($this->once())
                ->method('add')
                ->with($this->consultantNote);
        $this->execute();
    }
    public function test_execute_setPayloadSubmittedNoteId()
    {
        $this->execute();
        $this->assertSame($this->consultantNoteId, $this->payload->submittedNoteId);
    }
}
