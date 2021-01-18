<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use Client\Domain\Model\Client;
use Tests\TestBase;

class RemoveCVTest extends TestBase
{
    protected $clientRepository, $client;
    protected $clientCVRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $clientCVFormId = "clientCVFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
        
        $this->clientCVRepository = $this->buildMockOfInterface(ClientCVRepository::class);
        
        $this->service = new RemoveCV($this->clientRepository, $this->clientCVRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->clientCVFormId);
    }
    public function test_execute_removeCVInClient()
    {
        $this->clientCVRepository->expects($this->once())
                ->method("aClientCVCorrespondWithCVForm")
                ->with($this->clientId, $this->clientCVFormId);
        $this->client->expects($this->once())
                ->method("removeCV");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientCVRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
