<?php

namespace Personnel\Domain\Task\Coordinator;

use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class RemoveNoteTest extends CoordinatorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorNoteDependency();
        
        $this->task = new RemoveNote($this->coordinatorNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->coordinator, $this->coordinatorNoteId);
    }
    public function test_execute_removeNote()
    {
        $this->coordinatorNote->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_assertCoordinatorNoteManageableByCoordinator()
    {
        $this->coordinatorNote->expects($this->once())
                ->method('assertManageableByCoordinator');
        $this->execute();
    }
}
