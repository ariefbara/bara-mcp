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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenCommentSubmittedByConsultant $generateNotificationWhenCommentSubmittedByConsultant,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenCommentSubmittedByConsultant = $generateNotificationWhenCommentSubmittedByConsultant;
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
        $this->generateNotificationWhenCommentSubmittedByConsultant->execute($commentId);
    }

}
