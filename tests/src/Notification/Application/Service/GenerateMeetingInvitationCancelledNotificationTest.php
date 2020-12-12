<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Tests\TestBase;

class GenerateMeetingInvitationCancelledNotificationTest extends TestBase
{
    protected $meetingAttendee, $meetingAttendeeRepository;
    protected $service;
    protected $meetingAttendeeId = "meetingAttendeeId";
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->meetingAttendee = $this->buildMockOfClass(MeetingAttendee::class);
        $this->meetingAttendeeRepository = $this->buildMockOfInterface(MeetingAttendeeRepository::class);
        $this->meetingAttendeeRepository->expects($this->any())
                ->method("ofId")
                ->with($this->meetingAttendeeId)
                ->willReturn($this->meetingAttendee);
        
        $this->service = new GenerateMeetingInvitationCancelledNotification($this->meetingAttendeeRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->meetingAttendeeId);
    }
    public function test_execute_addInvitationCancelledNotificationInMeetingAttendee()
    {
        $this->meetingAttendee->expects($this->once())
                ->method("addInvitationCancelledNotification");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->meetingAttendeeRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
