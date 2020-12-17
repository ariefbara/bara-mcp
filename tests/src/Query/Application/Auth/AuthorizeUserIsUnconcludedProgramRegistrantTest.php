<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeUserIsUnconcludedProgramRegistrantTest extends TestBase
{
    protected $userRegistrantRepository;
    protected $authZ;
    protected $userId = "userId", $firmId = "firmId", $programId = "programId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRegistrantRepository = $this->buildMockOfInterface(UserRegistrantRepository::class);
        $this->authZ = new AuthorizeUserIsUnconcludedProgramRegistrant($this->userRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->userId, $this->firmId, $this->programId);
    }
    public function test_execute_noRecordOfUnconcludedRegistrant_forbidden()
    {
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only unconcluded program regsitrant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_execute_userInUnconcludedProgramRegistrant_void()
    {
        $this->userRegistrantRepository->expects($this->once())
                ->method("containRecordOfUnconcludedRegistrationToProgram")
                ->with($this->userId, $this->firmId, $this->programId)
                ->willReturn(true);
        $this->execute();
    }
}
