<?php

namespace Firm\Application\Service\User\MeetingAttendee;

use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Tests\TestBase;

class CancelInvitationTest extends TestBase
{
    protected $attendeeRepository, $initiator, $attendee;
    protected $consultant, $consultantRepository;
    protected $service;
    protected $userId = "userId", $meetingId = "meetingId", $attendeeId = "attendeeId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->initiator = $this->buildMockOfClass(Attendee::class);
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToUserParticipantCorrespondWithMeeting")
                ->with($this->userId, $this->meetingId)
                ->willReturn($this->initiator);
        $this->attendeeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->attendeeId)
                ->willReturn($this->attendee);
        
        $this->service = new CancelInvitation($this->attendeeRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->meetingId, $this->attendeeId);
    }
    public function test_execute_executeInitiatorsCancelInvitation()
    {
        $this->initiator->expects($this->once())
                ->method("cancelInvitationTo")
                ->with($this->attendee);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->attendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
