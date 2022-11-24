<?php

namespace Personnel\Domain\Task\Coordinator;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class UpdateNoteTest extends CoordinatorTaskTestBase
{
    protected $task;
    protected $payload, $labelData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorNoteDependency();
        
        $this->task = new UpdateNote($this->coordinatorNoteRepository);
        $this->labelData = new LabelData('name', 'description');
        $this->payload = new UpdateNotePayload($this->coordinatorNoteId, $this->labelData);
    }
    
    protected function execute()
    {
        $this->task->execute($this->coordinator, $this->payload);
    }
    public function test_execute_updateNote()
    {
        $this->coordinatorNote->expects($this->once())
                ->method('update')
                ->with($this->labelData);
        $this->execute();
    }
    public function test_execute_assertCoordinatorNoteManageableByCoordinator()
    {
        $this->coordinatorNote->expects($this->once())
                ->method('assertManageableByCoordinator')
                ->with($this->coordinator);
        $this->execute();
    }
}
