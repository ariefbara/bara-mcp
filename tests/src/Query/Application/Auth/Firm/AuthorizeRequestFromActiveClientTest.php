<?php

namespace Query\Application\Auth\Firm;

use Tests\TestBase;

class AuthorizeRequestFromActiveClientTest extends TestBase
{
    protected $clientRepository;
    protected $authZ;
    protected $firmId = "firmId", $clientId = "clientId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->authZ = new AuthorizeRequestFromActiveClient($this->clientRepository);
    }
    
    protected function execute()
    {
        $this->clientRepository->expects($this->any())
                ->method("containRecordOfActiveClientInFirm")
                ->willReturn(true);
        $this->authZ->execute($this->firmId, $this->clientId);
    }
    public function test_execute_containRecordOfActiveClient_void()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfActiveClient_forbidden()
    {
        $this->clientRepository->expects($this->once())
                ->method("containRecordOfActiveClientInFirm")
                ->with($this->firmId, $this->clientId)
                ->willReturn(false);
        $operation = function(){
            $this->execute();
        };
        $errorDetail = "forbidden: only active client can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
}
