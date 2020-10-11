<?php

namespace Notification\Application\Listener\Firm\Team;

use Notification\ {
    Application\Service\GenerateConsultationRequestNotificationTriggeredByTeamMember,
    Application\Service\SendImmediateMail,
    Domain\Model\Firm\Program\Participant\ConsultationRequest
};
use Resources\Application\Event\ {
    Event,
    Listener
};

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
        $state = ConsultationRequest::SUBMITTED_BY_PARTICIPANT;
        $this->generateConsultationRequestNotificationTriggeredByTeamMember
                ->execute($memberId, $consultationRequestId, $state);
    }

}
