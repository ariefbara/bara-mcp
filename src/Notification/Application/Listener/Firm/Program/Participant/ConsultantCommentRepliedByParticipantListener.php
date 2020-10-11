<?php

namespace Notification\Application\Listener\Firm\Program\Participant;

use Notification\Application\Service\ {
    GenerateConsultantCommentRepliedByParticipantNotification,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultantCommentRepliedByParticipantListener implements Listener
{
    /**
     *
     * @var GenerateConsultantCommentRepliedByParticipantNotification
     */
    protected $generateConsultantCommentRepliedByParticipantNotification;
    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;
    
    public function __construct(GenerateConsultantCommentRepliedByParticipantNotification $generateConsultantCommentRepliedByParticipantNotification,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateConsultantCommentRepliedByParticipantNotification = $generateConsultantCommentRepliedByParticipantNotification;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    
    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $commentId = $event->getId();
        $this->generateConsultantCommentRepliedByParticipantNotification->execute($commentId);
    }

}
