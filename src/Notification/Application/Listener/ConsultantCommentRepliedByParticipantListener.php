<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultantCommentRepliedByParticipant,
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
     * @var GenerateNotificationWhenConsultantCommentRepliedByParticipant
     */
    protected $generateNotificationWhenConsultantCommentRepliedByParticipant;

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultantCommentRepliedByParticipant $generateNotificationWhenConsultantCommentRepliedByParticipant,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultantCommentRepliedByParticipant = $generateNotificationWhenConsultantCommentRepliedByParticipant;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    public function execute(CommonEvent $event): void
    {
        $commentId = $event->getId();
        $this->generateNotificationWhenConsultantCommentRepliedByParticipant->execute($commentId);
    }

}
