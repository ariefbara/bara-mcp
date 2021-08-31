<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingScheduleChangedNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class MeetingScheduleChangedListener implements Listener
{

    /**
     * 
     * @var GenerateMeetingScheduleChangedNotification
     */
    protected $generateMeetingScheduleChangeNotification;
    
    function __construct(GenerateMeetingScheduleChangedNotification $generateMeetingScheduleChangeNotification)
    {
        $this->generateMeetingScheduleChangeNotification = $generateMeetingScheduleChangeNotification;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $meetingId = $event->getId();
        $this->generateMeetingScheduleChangeNotification->execute($meetingId);
    }

}
