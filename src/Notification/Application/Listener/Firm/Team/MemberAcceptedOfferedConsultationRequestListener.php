<?php

namespace Notification\Application\Listener\Firm\Team;

use Notification\Application\Service\ {
    AddConsultationSessionScheduledNotificationTriggeredByTeamMember,
    SendImmediateMail
};
use Resources\Application\Event\ {
    Event,
    Listener
};

class MemberAcceptedOfferedConsultationRequestListener implements Listener
{

    /**
     *
     * @var AddConsultationSessionScheduledNotificationTriggeredByTeamMember
     */
    protected $addConsultationSessionScheduledNotificationTriggeredByTeamMember;

    public function __construct(
            AddConsultationSessionScheduledNotificationTriggeredByTeamMember $addConsultationSessionScheduledNotificationTriggeredByTeamMember)
    {
        $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember = $addConsultationSessionScheduledNotificationTriggeredByTeamMember;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(TriggeredByTeamMemberEventInterface $event): void
    {
        $memberId = $event->getMemberId();
        $consultationSessionId = $event->getId();
        $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember->execute($memberId, $consultationSessionId);
    }

}
