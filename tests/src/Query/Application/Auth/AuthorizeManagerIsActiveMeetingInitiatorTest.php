<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeManagerIsActiveMeetingInitiatorTest extends TestBase
{
    protected $meetingAttendeeRepository;
    protected $authZ;
    protected $firmId = "firmId", $managerId = "managerId", $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->authZ = new AuthorizeManagerIsActiveMeetingInitiator($this->meetingAttendeeRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->managerId, $this->meetingId);
    }
    public function test_execute_noActiveAttendeeRecordCorrespondWithManager_forbidden()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("containRecordOfActiveMeetingAttendeeCorrespondWithManagerWithInitiatorRole")
                ->with($this->firmId, $this->managerId, $this->meetingId)
                ->willReturn(false);
        
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only meeting initiator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
