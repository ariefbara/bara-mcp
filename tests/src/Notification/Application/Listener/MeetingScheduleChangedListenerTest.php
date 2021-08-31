<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingScheduleChangedNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class MeetingScheduleChangedListenerTest extends TestBase
{
    protected $generateMeetingScheduleChangeNotification;
    protected $listener;
    protected $event, $meetingId = "meetingId";
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->generateMeetingScheduleChangeNotification = $this->buildMockOfClass(GenerateMeetingScheduleChangedNotification::class);
        
        $this->listener = new MeetingScheduleChangedListener($this->generateMeetingScheduleChangeNotification);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
    }
    
    protected function executeHandle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeGenerateMeetingScheduleChangedNotification()
    {
        $this->event->expects($this->once())->method("getId")->willReturn($this->meetingId);
        $this->generateMeetingScheduleChangeNotification->expects($this->once())
                ->method("execute")
                ->with($this->meetingId);
        $this->executeHandle();
    }
}
