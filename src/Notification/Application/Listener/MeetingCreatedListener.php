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

    /**
     * 
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    function __construct(GenerateMeetingCreatedNotification $generateMeetingCreaterNotification,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateMeetingCreaterNotification = $generateMeetingCreaterNotification;
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
        $this->generateMeetingCreaterNotification->execute($meetingId);
    }

}
