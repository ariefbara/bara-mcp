<?php

namespace Notification\Application\Listener\Firm\Program\Participant\Worksheet;

use Notification\Application\Service\Firm\Program\Participant\Worksheet\SendClientParticipantRepliedConsultantCommentMail;
use Resources\Application\Event\ {
    Event,
    Listener
};

class ConsultantCommentRepliedByClientParticipantListener implements Listener
{

    /**
     *
     * @var SendClientParticipantRepliedConsultantCommentMail
     */
    protected $sendClientParticipantRepliedConsultantCommentMail;

    public function __construct(SendClientParticipantRepliedConsultantCommentMail $sendClientParticipantRepliedConsultantCommentMail)
    {
        $this->sendClientParticipantRepliedConsultantCommentMail = $sendClientParticipantRepliedConsultantCommentMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantCommentRepliedByClientParticipantEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $clientId = $event->getClientId();
        $programParticipationId = $event->getProgramParticipationId();
        $worksheetId = $event->getWorksheetId();
        $commentId = $event->getCommentId();
        
        $this->sendClientParticipantRepliedConsultantCommentMail
                ->execute($firmId, $clientId, $programParticipationId, $worksheetId, $commentId);
    }

}
