<?php

namespace Personnel\Domain\Task\Coordinator;

use SharedContext\Domain\ValueObject\LabelData;
use Tests\src\Personnel\Domain\Task\Coordinator\CoordinatorTaskTestBase;

class UpdateTaskTest extends CoordinatorTaskTestBase
{

    protected $service;
    protected $payload, $labelData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCoordinatorTaskDependency();

        $this->service = new UpdateTask($this->coordinatorTaskRepository);

        $this->labelData = new LabelData('name', 'description');
        $this->payload = new UpdateTaskPayload($this->coordinatorTaskId, $this->labelData);
    }
    
    protected function execute()
    {
        $this->service->execute($this->coordinator, $this->payload);
    }
    public function test_execute_updateCoordinatorTask()
    {
        $this->coordinatorTask->expects($this->once())
                ->method('update')
                ->with($this->labelData);
        
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
