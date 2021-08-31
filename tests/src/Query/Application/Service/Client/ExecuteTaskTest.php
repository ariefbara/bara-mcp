<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Tests\src\Query\Application\Service\Client\ClientTestBase;

class ExecuteTaskTest extends ClientTestBase
{
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteTask($this->clientRepository);
        
        $this->task = $this->buildMockOfClass(ITaskExecutableByClient::class);
    }
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->task);
    }
    public function test_execute_clientExecuteTask()
    {
        $this->client->expects($this->once())
                ->method('executeTask')
                ->with($this->task);
        $this->execute();
    }
}
