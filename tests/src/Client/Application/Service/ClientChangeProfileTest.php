<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;
use Tests\TestBase;

class ClientChangeProfileTest extends TestBase
{
    protected $service;
    protected $clientRepository, $client, $clientId = 'clientId';
    protected $name = 'new client name';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->service = new ClientChangeProfile($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientId, $this->name);
    }
    public function test_execute_changeClientProfile()
    {
        $this->client->expects($this->once())
                ->method('changeProfile')
                ->with($this->name);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
