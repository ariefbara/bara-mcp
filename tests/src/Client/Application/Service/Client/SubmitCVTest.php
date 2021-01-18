<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use Client\Domain\Model\Client;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitCVTest extends TestBase
{
    protected $clientRepository, $client;
    protected $clientCVFormRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $clientCVFormId = "clientCVFormId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
        
        $this->clientCVFormRepository = $this->buildMockOfInterface(ClientCVFormRepository::class);
        
        $this->service = new SubmitCV($this->clientRepository, $this->clientCVFormRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->clientCVFormId, $this->formRecordData);
    }
    public function test_execute_submitCVInClient()
    {
        $this->clientCVFormRepository->expects($this->once())->method("ofId")->with($this->clientCVFormId);
        $this->client->expects($this->once())
                ->method("submitCV")
                ->with($this->anything(), $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
