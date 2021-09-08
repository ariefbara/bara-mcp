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

    function __construct(GenerateMeetingInvitationCancelledNotification $generateMeetingInvitationCancelledNotification)
    {
        $this->generateMeetingInvitationCancelledNotification = $generateMeetingInvitationCancelledNotification;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $meetingAttendeeId = $event->getId();
        $this->generateMeetingInvitationCancelledNotification->execute($meetingAttendeeId);
    }

}
