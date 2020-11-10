<?php

namespace Query\Application\Auth\Firm;

use Tests\TestBase;

class AuthorizeUserIsActiveParticipantInFirmTest extends TestBase
{
    protected $participantRepository;
    protected $authZ;
    protected $firmId = "firmId", $userId = "userId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->authZ = new AuthorizeUserIsActiveParticipantInFirm($this->participantRepository);
    }
    
    public function execute()
    {
        $this->participantRepository->expects($this->any())
                ->method("containRecordOfParticipantInFirmCorrespondWithUser")
                ->willReturn(true);
        $this->authZ->execute($this->firmId, $this->userId);
    }
    public function test_execute_containRecordOfParticipantCorrespondWithUser_void()
    {
        $this->execute();
        $this->markAsSuccess();
    }
    public function test_execute_noRecordOfParticipantCorrespondWithUser_forbidden()
    {
        $this->participantRepository->expects($this->once())
                ->method("containRecordOfParticipantInFirmCorrespondWithUser")
                ->with($this->firmId, $this->userId)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only active user participating in firm's program can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
}
