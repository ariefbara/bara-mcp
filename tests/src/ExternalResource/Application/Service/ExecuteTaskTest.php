<?php

namespace ExternalResource\Application\Service;

use ExternalResource\Domain\Model\ExternalEntity;
use ExternalResource\Domain\Model\ExternalTask;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $externalEntity;
    protected $service;
    protected $task, $payload = 'string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->externalEntity = $this->buildMockOfInterface(ExternalEntity::class);
        $this->service = new ExecuteTask($this->externalEntity);
        
        $this->task = $this->buildMockOfInterface(ExternalTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->task, $this->payload);
    }
    public function test_execute_externalEntitiyExecuteTask()
    {
        $this->externalEntity->expects($this->once())
                ->method('executeExternalTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
}
