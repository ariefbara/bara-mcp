<?php

namespace Personnel\Application\Service\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Task\Coordinator\CoordinatorTask;
use Tests\TestBase;

class ExecuteCoordinatorTaskTest extends TestBase
{
    protected $coordinatorRepository, $coordinator, $personnelId = 'personnelId', $coordinatorId = 'coordinatorId';
    protected $service;
    //
    protected $task, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository->expects($this->any())
                ->method('aCoordinatorBelongsToPersonnel')
                ->with($this->personnelId, $this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->service = new ExecuteCoordinatorTask($this->coordinatorRepository);
        //
        $this->task = $this->buildMockOfInterface(CoordinatorTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->personnelId, $this->coordinatorId, $this->task, $this->payload);
    }
    public function test_execute_coordinatorExecuteTask()
    {
        $this->coordinator->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
