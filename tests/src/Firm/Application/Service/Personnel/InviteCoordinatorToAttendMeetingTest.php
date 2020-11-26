<?php

namespace Firm\Application\Service\Personnel;

use Firm\ {
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Personnel\AttendeeRepository,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee
};
use Tests\TestBase;

class InviteCoordinatorToAttendMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $coordinator, $coordinatorRepository;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $coordinatorId = "coordinatorId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
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
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->coordinatorId);
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
