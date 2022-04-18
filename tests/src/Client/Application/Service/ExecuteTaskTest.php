<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Client\Domain\Model\IClientTask;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $clientRepository, $client, $clientId = 'client-id';
    protected $service;
    protected $task;
    protected $payload = 'random string represent task payload';


    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('aClientOfId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->service = new ExecuteTask($this->clientRepository);
        $this->task = $this->buildMockOfInterface(IClientTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->task, $this->payload);
    }
    public function test_execute_clientExecuteTask()
    {
        $this->client->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
