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

    /**
     * 
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;
    
    function __construct(GenerateMeetingScheduleChangedNotification $generateMeetingScheduleChangeNotification,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateMeetingScheduleChangeNotification = $generateMeetingScheduleChangeNotification;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $meetingId = $event->getId();
        $this->generateMeetingScheduleChangeNotification->execute($meetingId);
    }

}
