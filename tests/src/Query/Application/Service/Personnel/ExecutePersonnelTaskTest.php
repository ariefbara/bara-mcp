<?php

namespace Query\Application\Service\Personnel;

use Query\Domain\Task\Personnel\PersonnelTask;
use Tests\src\Query\Application\Service\Personnel\PersonnelTestBase;

class ExecutePersonnelTaskTest extends PersonnelTestBase
{
    protected $service;
    protected $task, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecutePersonnelTask($this->personnelRepository);
        
        $this->task = $this->buildMockOfInterface(PersonnelTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->task, $this->payload);
    }
    public function test_execute_personnelExecutePersonnelTask()
    {
        $this->personnel->expects($this->once())
                ->method('executePersonnelTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    
}
