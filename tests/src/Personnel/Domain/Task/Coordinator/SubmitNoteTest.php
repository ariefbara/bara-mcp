<?php

namespace Personnel\Domain\Task\Coordinator;

use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class SubmitNoteTest extends CoordinatorTaskTestBase
{
    protected $task;
    protected $payload, $content = 'note content', $viewableByParticipant = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorNoteDependency();
        $this->setUpParticipantDependency();
        
        $this->task = new SubmitNote($this->coordinatorNoteRepository, $this->participantRepository);
        $this->payload = new SubmitNotePayload($this->participantId, $this->content, $this->viewableByParticipant);
    }
    
    protected function execute()
    {
        $this->coordinatorNoteRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->coordinatorNoteId);
        
        $this->task->execute($this->coordinator, $this->payload);
    }
    public function test_execute_addCoordinatorNoteSubmittedByCoordinatorToRepository()
    {
        $this->coordinator->expects($this->once())
                ->method('submitNote')
                ->with($this->coordinatorNoteId, $this->participant, $this->content, $this->viewableByParticipant)
                ->willReturn($this->coordinatorNote);
        
        $this->coordinatorNoteRepository->expects($this->once())
                ->method('add')
                ->with($this->coordinatorNote);
        
        $this->execute();
    }
    public function test_execute_setPayloadsSubmittedNoteId()
    {
        $this->execute();
        $this->assertSame($this->coordinatorNoteId, $this->payload->submittedNoteId);
    }
}
