<?php

namespace Personnel\Domain\Task\Coordinator;

use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class HideNoteFromParticipantTest extends CoordinatorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorNoteDependency();
        
        $this->task = new HideNoteFromParticipant($this->coordinatorNoteRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->coordinator, $this->coordinatorNoteId);
    }
    public function test_execute_hideNoteFromParticipant()
    {
        $this->coordinatorNote->expects($this->once())
                ->method('hideFromParticipant');
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
