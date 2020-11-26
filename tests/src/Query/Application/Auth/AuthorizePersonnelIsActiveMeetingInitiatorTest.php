<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizePersonnelIsActiveMeetingInitiatorTest extends TestBase
{
    protected $meetingAttendeeRepository;
    protected $authZ;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->authZ = new AuthorizePersonnelIsActiveMeetingInitiator($this->meetingAttendeeRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->personnelId, $this->meetingId);
    }
    public function test_execute_noActiveAttendeeRecordCorrespondWithPersonnel_forbidden()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("containRecordOfActiveMeetingAttendeeCorrespondWithPersonnelWithInitiatorRole")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn(false);
        
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only meeting initiator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
