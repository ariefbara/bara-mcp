<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use Client\Domain\Model\Client;
use Tests\src\Client\Application\Service\Client\ClientTestBase;

class RemoveBioTest extends ClientTestBase
{
    protected $clientBioRepository;
    protected $service;
    protected $bioFormId = "bioFormId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientBioRepository = $this->buildMockOfInterface(ClientBioRepository::class);
        
        $this->service = new RemoveBio($this->clientRepository, $this->clientBioRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->bioFormId);
    }
    public function test_execute_removeCVInClient()
    {
        $this->clientBioRepository->expects($this->once())
                ->method("aBioCorrespondWithForm")
                ->with($this->clientId, $this->bioFormId);
        $this->client->expects($this->once())
                ->method("removeBio");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientBioRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
