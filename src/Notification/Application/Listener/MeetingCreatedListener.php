<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingCreatedNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class MeetingCreatedListener implements Listener
{

    /**
     * 
     * @var GenerateMeetingCreatedNotification
     */
    protected $generateMeetingCreaterNotification;

    function __construct(GenerateMeetingCreatedNotification $generateMeetingCreaterNotification)
    {
        $this->generateMeetingCreaterNotification = $generateMeetingCreaterNotification;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $meetingId = $event->getId();
        $this->generateMeetingCreaterNotification->execute($meetingId);
    }

}
