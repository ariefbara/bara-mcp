<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\ {
    Application\Service\Firm\Program\CoordinatorRepository,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class InviteCoordinatorToAttendMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $coordinator, $coordinatorRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $meetingId = "meetingId", $coordinatorId = "coordinatorId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToClientParticipantCorrespondWithMeeting")
                ->with($this->firmId, $this->clientId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->once())
                ->method("aCoordinatorOfId")
                ->with($this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->service = new InviteCoordinatorToAttendMeeting($this->attendeeRepository, $this->coordinatorRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->meetingId, $this->coordinatorId);
    }
    public function test_execute_inviteCoordinatorToAttendMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->coordinator);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->attendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
