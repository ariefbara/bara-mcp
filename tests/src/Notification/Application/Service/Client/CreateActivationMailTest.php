<?php

namespace Notification\Application\Service\Client;

use Notification\Domain\Model\Firm\Client;
use Tests\TestBase;

class CreateActivationMailTest extends TestBase
{
    protected $clientMailRepository, $nextId = "nextId";
    protected $clientRepository, $client;
    protected $service;
    protected $clientId = "clientId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMailRepository = $this->buildMockOfInterface(ClientMailRepository::class);
        $this->clientMailRepository->expects($this->any())
                ->method("nextIdentity")->willReturn($this->nextId);
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method("ofId")
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->service = new CreateActivationMail($this->clientMailRepository, $this->clientRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->clientId);
    }
    public function test_execute_addClientMailToRepository()
    {
        $this->client->expects($this->once())
                ->method("createActivationMail")
                ->with($this->nextId);
        $this->clientMailRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
}
