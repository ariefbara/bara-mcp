<?php

namespace Firm\Application\Service;

use Firm\Domain\Model\ResponsiveTask;
use Tests\TestBase;

class ExecuteResponsiveTaskTest extends TestBase
{
    protected $repository;
    protected $service;
    protected $task, $payload = 'string represent payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->buildMockOfInterface(GenericRepository::class);
        $this->service = new ExecuteResponsiveTask($this->repository);
        
        $this->task = $this->buildMockOfInterface(ResponsiveTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->task, $this->payload);
    }
    public function test_execute_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->repository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
