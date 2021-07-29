<?php

namespace Firm\Application\Service\Manager;

use Firm\Application\Service\Firm\Program\ParticipantRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class InviteParticipantToAttendMeetingTest extends TestBase
{
    protected $attendeeRepository, $attendee;
    protected $participant, $participantRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $meetingId = "meetingId", $participantId = "participantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToManagerCorrespondWithMeeting")
                ->with($this->firmId, $this->managerId, $this->meetingId)
                ->willReturn($this->attendee);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->participantRepository->expects($this->once())
                ->method("ofId")
                ->with($this->participantId)
                ->willReturn($this->participant);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new InviteParticipantToAttendMeeting($this->attendeeRepository, $this->participantRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->meetingId, $this->participantId);
    }
    public function test_execute_inviteParticipantToAttendMeeting()
    {
        $this->attendee->expects($this->once())
                ->method("inviteUserToAttendMeeting")
                ->with($this->participant);
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
                ->with($this->attendee);
        $this->execute();
    }
}
