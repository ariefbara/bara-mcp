<?php

namespace Personnel\Domain\Task\Coordinator;

use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class ApproveTaskReportTest extends CoordinatorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorTaskDependency();
        
        //
        $this->task = new ApproveTaskReport($this->coordinatorTaskRepository);
    }
    
    //
    protected function execute()
    {
        $this->task->execute($this->coordinator, $this->coordinatorTaskId);
    }
    public function test_execute_approveTaskReport()
    {
        $this->coordinatorTask->expects($this->once())
                ->method('approveReport');
        $this->execute();
    }
    public function test_execute_assertCoordinatorTaskManageableByCoordinator()
    {
        $this->coordinatorTask->expects($this->once())
                ->method('assertManageableByCoordinator')
                ->with($this->coordinator);
        $this->execute();
    }
}
