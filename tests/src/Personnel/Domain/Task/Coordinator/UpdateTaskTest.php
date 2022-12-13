<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class UpdateTaskTest extends CoordinatorTaskTestBase
{

    protected $service;
    protected $payload, $taskData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorTaskDependency();

        $this->service = new UpdateTask($this->coordinatorTaskRepository);

        $this->taskData = $this->buildMockOfClass(TaskData::class);
        $this->payload = new UpdateTaskPayload($this->coordinatorTaskId, $this->taskData);
    }
    
    protected function execute()
    {
        $this->service->execute($this->coordinator, $this->payload);
    }
    public function test_execute_updateCoordinatorTask()
    {
        $this->coordinatorTask->expects($this->once())
                ->method('update')
                ->with($this->taskData);
        
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
