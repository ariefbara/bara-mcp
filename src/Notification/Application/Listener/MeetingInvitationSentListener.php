<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingInvitationSentNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class MeetingInvitationSentListener implements Listener
{

    /**
     * 
     * @var GenerateMeetingInvitationSentNotification
     */
    protected $generateMeetingInvitationSentNotification;

    /**
     * 
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;
    
    function __construct(GenerateMeetingInvitationSentNotification $generateMeetingInvitationSentNotification,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateMeetingInvitationSentNotification = $generateMeetingInvitationSentNotification;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $meetingAttendeeId = $event->getId();
        $this->generateMeetingInvitationSentNotification->execute($meetingAttendeeId);
    }

}
