<?php

namespace Query\Application\Auth;

use Tests\TestBase;

class AuthorizeTeamIsActiveMeetingInitiatorTest extends TestBase
{
    protected $meetingAttendeeRepository;
    protected $authZ;
    protected $firmId = "firmId", $teamId = "teamId", $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->authZ = new AuthorizeTeamIsActiveMeetingInitiator($this->meetingAttendeeRepository);
    }
    
    protected function execute()
    {
        $this->authZ->execute($this->firmId, $this->teamId, $this->meetingId);
    }
    public function test_execute_noActiveAttendeeRecordCorrespondWithTeam_forbidden()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("containRecordOfActiveMeetingAttendeeCorrespondWithTeamAsProgramParticipantHavingInitiatorRole")
                ->with($this->firmId, $this->teamId, $this->meetingId)
                ->willReturn(false);
        
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "forbidden: only meeting initiator can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}
