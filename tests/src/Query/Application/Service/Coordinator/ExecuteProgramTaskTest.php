<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Tests\src\Query\Application\Service\Coordinator\CoordinatorQueryTestBase;

class ExecuteProgramTaskTest extends CoordinatorQueryTestBase
{
    protected $service;
    protected $task, $payload = 'string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteProgramTask($this->coordinatorRepository);
        
        $this->task = $this->buildMockOfInterface(ProgramTaskExecutableByCoordinator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->coordinatorId, $this->task, $this->payload);
    }
    public function test_execute_coordinatorExecuteProgramTask()
    {
        $this->coordinator->expects($this->once())
                ->method('executeProgramTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
}
