<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Personnel\Domain\Task\Mentor\MentorTaskTestBase;

class SubmitNoteTest extends MentorTaskTestBase
{
    protected $task;
    protected $payload, $labelData, $viewableByParticipant = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setParticipantRelatedTask();
        $this->setConsultantNoteRelatedTask();
        
        $this->labelData = new LabelData('name', 'description');
        
        $this->task = new SubmitNote($this->consultantNoteRepository, $this->participantRepository);
        
        $this->payload = new SubmitNotePayload($this->participantId, $this->labelData, $this->viewableByParticipant);
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
                ->with($this->consultantNoteId, $this->participant, $this->labelData, $this->viewableByParticipant)
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
