<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenCommentSubmittedByConsultant,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class CommentSubmittedByConsultantListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenCommentSubmittedByConsultant
     */
    protected $generateNotificationWhenCommentSubmittedByConsultant;

    public function __construct(
            GenerateNotificationWhenCommentSubmittedByConsultant $generateNotificationWhenCommentSubmittedByConsultant)
    {
        $this->generateNotificationWhenCommentSubmittedByConsultant = $generateNotificationWhenCommentSubmittedByConsultant;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $commentId = $event->getId();
        $this->generateNotificationWhenCommentSubmittedByConsultant->execute($commentId);
    }

}
