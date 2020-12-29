<?php

namespace Notification\Application\Listener\Firm\Team;

use Notification\Application\Service\GenerateConsultationRequestNotificationTriggeredByTeamMember;
use Notification\Application\Service\SendImmediateMail;
use Resources\Application\Event\Event;
use Resources\Application\Event\Listener;
use SharedContext\Domain\ValueObject\MailMessageBuilder;

class MemberSubmittedConsultationRequestListener implements Listener
{

    /**
     *
     * @var GenerateConsultationRequestNotificationTriggeredByTeamMember
     */
    protected $generateConsultationRequestNotificationTriggeredByTeamMember;

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateConsultationRequestNotificationTriggeredByTeamMember $generateConsultationRequestNotificationTriggeredByTeamMember,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateConsultationRequestNotificationTriggeredByTeamMember = $generateConsultationRequestNotificationTriggeredByTeamMember;
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
        $consultationRequestId = $event->getId();
        $state = MailMessageBuilder::CONSULTATION_REQUESTED;
        $this->generateConsultationRequestNotificationTriggeredByTeamMember
                ->execute($memberId, $consultationRequestId, $state);
    }

}
