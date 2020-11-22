<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeUserIsActiveMeetingInitiatorTest extends TestBase
{
    protected $meetingAttendeeRepository;
    protected $authZ;
    protected $userId = "userId", $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->authZ = new AuthorizeUserIsActiveMeetingInitiator($this->meetingAttendeeRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->userId, $this->meetingId);
    }
    public function test_execute_noActiveAttendeeRecordCorrespondWithUser_forbidden()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("containRecordOfActiveMeetingAttendeeCorrespondWithUserAsProgramParticipantHavingInitiatorRole")
                ->with($this->userId, $this->meetingId)
                ->willReturn(false);
        
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only meeting initiator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
