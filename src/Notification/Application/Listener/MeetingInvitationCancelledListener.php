<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\GenerateMeetingInvitationCancelledNotification;
use Notification\Application\Service\SendImmediateMail;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use Resources\Domain\Event\CommonEvent;

class MeetingInvitationCancelledListener implements Listener
{

    /**
     * 
     * @var GenerateMeetingInvitationCancelledNotification
     */
    protected $generateMeetingInvitationCancelledNotification;

    /**
     * 
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    function __construct(GenerateMeetingInvitationCancelledNotification $generateMeetingInvitationCancelledNotification,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateMeetingInvitationCancelledNotification = $generateMeetingInvitationCancelledNotification;
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
        $this->generateMeetingInvitationCancelledNotification->execute($meetingAttendeeId);
    }

}
