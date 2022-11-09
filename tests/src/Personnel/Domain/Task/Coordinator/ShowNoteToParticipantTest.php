<?php

namespace Personnel\Domain\Task\Coordinator;

use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class ShowNoteToParticipantTest extends CoordinatorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorNoteDependency();
        
        $this->task = new ShowNoteToParticipant($this->coordinatorNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->coordinator, $this->coordinatorNoteId);
    }
    public function test_execute_showNoteToParticipant()
    {
        $this->coordinatorNote->expects($this->once())
                ->method('showToParticipant');
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
