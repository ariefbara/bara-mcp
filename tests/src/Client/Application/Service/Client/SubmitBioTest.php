<?php

namespace Client\Application\Service\Client;

use Client\Application\Service\ClientRepository;
use Client\Domain\Model\Client;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\src\Client\Application\Service\Client\ClientTestBase;

class SubmitBioTest extends ClientTestBase
{
    protected $bioFormRepository;
    protected $service;
    protected $bioFormId = "clientCVFormId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bioFormRepository = $this->buildMockOfInterface(BioFormRepository::class);
        
        $this->service = new SubmitBio($this->clientRepository, $this->bioFormRepository);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->bioFormId, $this->formRecordData);
    }
    public function test_execute_submitCVInClient()
    {
        $this->bioFormRepository->expects($this->once())->method("ofId")->with($this->bioFormId);
        $this->client->expects($this->once())
                ->method("submitBio")
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
