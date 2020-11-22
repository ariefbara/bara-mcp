<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeClientIsActiveMeetingInitiatorTest extends TestBase
{
    protected $meetingAttendeeRepository;
    protected $authZ;
    protected $firmId = "firmId", $clientId = "clientId", $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->authZ = new AuthorizeClientIsActiveMeetingInitiator($this->meetingAttendeeRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->clientId, $this->meetingId);
    }
    public function test_execute_noActiveAttendeeRecordCorrespondWithClient_forbidden()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("containRecordOfActiveMeetingAttendeeCorrespondWithClientAsProgramParticipantHavingInitiatorRole")
                ->with($this->firmId, $this->clientId, $this->meetingId)
                ->willReturn(false);
        
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only meeting initiator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
