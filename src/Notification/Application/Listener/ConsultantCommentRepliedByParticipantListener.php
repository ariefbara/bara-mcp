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

    public function __construct(
            GenerateNotificationWhenConsultantCommentRepliedByParticipant $generateNotificationWhenConsultantCommentRepliedByParticipant)
    {
        $this->generateNotificationWhenConsultantCommentRepliedByParticipant = $generateNotificationWhenConsultantCommentRepliedByParticipant;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    public function execute(CommonEvent $event): void
    {
        $commentId = $event->getId();
        $this->generateNotificationWhenConsultantCommentRepliedByParticipant->execute($commentId);
    }

}
