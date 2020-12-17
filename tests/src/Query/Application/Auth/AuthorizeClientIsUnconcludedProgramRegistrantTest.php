<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeClientIsUnconcludedProgramRegistrantTest extends TestBase
{
    protected $clientRegistrantRepository;
    protected $authZ;
    protected $firmId = "firmId", $clientId = "clientId", $programId = "programId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->authZ = new AuthorizeClientIsUnconcludedProgramRegistrant($this->clientRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->clientId, $this->programId);
    }
    
    public function test_noUnconcludedProgramRegistrantCorrespondWithClient_forbidden()
    {
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only unconcluded registrant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_containRecordOfUnconcludedRegistrantCorrespondWithClient_void()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method("containRecordOfUnconcludedRegistrationToProgram")
                ->with($this->firmId, $this->clientId, $this->programId)
                ->willReturn(true);
        $this->execute();
    }
}
