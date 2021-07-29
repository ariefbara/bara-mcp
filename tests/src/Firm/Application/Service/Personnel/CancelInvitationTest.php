<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\Meeting\Attendee;
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class CancelInvitationTest extends TestBase
{
    protected $attendeeRepository, $initiator, $attendee;
    protected $consultant, $consultantRepository;
    protected $dispatcher;
    protected $service;
    protected $firmId = "firmId", $personnelId = "personnelId", $meetingId = "meetingId", $attendeeId = "attendeeId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->initiator = $this->buildMockOfClass(Attendee::class);
        $this->attendee = $this->buildMockOfClass(Attendee::class);
        $this->attendeeRepository = $this->buildMockOfInterface(AttendeeRepository::class);
        $this->attendeeRepository->expects($this->any())
                ->method("anAttendeeBelongsToPersonnelCorrespondWithMeeting")
                ->with($this->firmId, $this->personnelId, $this->meetingId)
                ->willReturn($this->initiator);
        $this->attendeeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->attendeeId)
                ->willReturn($this->attendee);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new CancelInvitation($this->attendeeRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->meetingId, $this->attendeeId);
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
    public function test_execute_dispatchAttendee()
    {
        $this->dispatcher->expects($this->once())
                ->method("dispatch")
                ->with($this->attendee);
        $this->execute();
    }
}
