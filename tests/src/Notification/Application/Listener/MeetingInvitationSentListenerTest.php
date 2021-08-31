<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingInvitationSentNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class MeetingInvitationSentListenerTest extends TestBase
{
    protected $generateMeetingInvitationSentNotification;
    protected $listener;
    protected $event, $meetingAttendeeId = "meetingAttendeeId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->generateMeetingInvitationSentNotification = $this->buildMockOfClass(GenerateMeetingInvitationSentNotification::class);
        
        $this->listener = new MeetingInvitationSentListener($this->generateMeetingInvitationSentNotification);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeGenerateMeetingInvitationSentNotification()
    {
        $this->event->expects($this->once())->method("getId")->willReturn($this->meetingAttendeeId);
        $this->generateMeetingInvitationSentNotification->expects($this->once())
                ->method("execute")
                ->with($this->meetingAttendeeId);
        $this->executeHandle();
    }
}
