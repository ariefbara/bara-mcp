<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;
use Tests\src\Firm\Application\Service\Coordinator\CoordinatorTestBase;

class ExecuteProgramTaskTest extends CoordinatorTestBase
{
    protected $service;
    protected $task;
    protected $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteProgramTask($this->coordinatorRepository);
        
        $this->task = $this->buildMockOfInterface(TaskInProgramExecutableByCoordinator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->task, $this->payload);
    }
    public function test_execute_coordinatorExecuteProgramTask()
    {
        $this->coordinator->expects($this->once())
                ->method('executeProgramTask')
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
