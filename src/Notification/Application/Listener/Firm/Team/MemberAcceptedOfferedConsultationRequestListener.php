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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;
    
    public function __construct(AddConsultationSessionScheduledNotificationTriggeredByTeamMember $addConsultationSessionScheduledNotificationTriggeredByTeamMember,
            SendImmediateMail $sendImmediateMail)
    {
        $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember = $addConsultationSessionScheduledNotificationTriggeredByTeamMember;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(TriggeredByTeamMemberEventInterface $event): void
    {
        $memberId = $event->getMemberId();
        $consultationSessionId = $event->getId();
        $this->addConsultationSessionScheduledNotificationTriggeredByTeamMember->execute($memberId, $consultationSessionId);
    }

}
