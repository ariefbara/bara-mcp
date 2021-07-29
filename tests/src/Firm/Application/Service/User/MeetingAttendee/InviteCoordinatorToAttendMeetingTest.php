<?php

namespace Firm\Application\Service\User\MeetingAttendee;

use Firm\Application\Service\Firm\Program\CoordinatorRepository;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteCoordinatorToAttendMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $coordinator, $coordinatorRepository;
    protected $dispatcher;
    protected $service;
    protected $userId = "userId", $meetingId = "meetingId", $coordinatorId = "coordinatorId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToUserParticipantCorrespondWithMeeting")
                ->with($this->userId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->once())
                ->method("aCoordinatorOfId")
                ->with($this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteCoordinatorToAttendMeeting($this->attendeeRepository, $this->coordinatorRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->meetingId, $this->coordinatorId);
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
    public function test_execute_dispatchAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->identicalTo($this->attendee));
        $this->execute();
    }
}
