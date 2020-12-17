<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeTeamIsUnconcludedProgramRegistrantTest extends TestBase
{
    protected $teamRegistrantRepository;
    protected $authZ;
    protected $firmId = "firmId", $teamId = "teamId", $programId = "programId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRegistrantRepository = $this->buildMockOfInterface(TeamRegistrantRepository::class);
        $this->authZ = new AuthorizeTeamIsUnconcludedProgramRegistrant($this->teamRegistrantRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->teamId, $this->programId);
    }
    public function test_execute_noRecordOfUnconcludedRegistration_forbidden()
    {
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only unconcluded program registrant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_execute_containRecordOfUnconcludedRegistration_void()
    {
        $this->teamRegistrantRepository->expects($this->once())
                ->method("containRecordOfUnconcludedRegistrationToProgram")
                ->with($this->firmId, $this->teamId, $this->programId)
                ->willReturn(true);
        $this->execute();
    }
}
