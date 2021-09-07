<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingCreatedNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class MeetingCreatedListenerTest extends TestBase
{
    protected $generateMeetingCreaterNotification;
    protected $listener;
    protected $event, $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->generateMeetingCreaterNotification = $this->buildMockOfClass(GenerateMeetingCreatedNotification::class);
        
        $this->listener = new MeetingCreatedListener($this->generateMeetingCreaterNotification);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    
    public function test_handle_executeGenerateMeetingCreatedNotification()
    {
        $this->event->expects($this->once())->method("getId")->willReturn($this->meetingId);
        $this->generateMeetingCreaterNotification->expects($this->once())
                ->method("execute")
                ->with($this->meetingId);
        $this->executeHandle();
    }
}
