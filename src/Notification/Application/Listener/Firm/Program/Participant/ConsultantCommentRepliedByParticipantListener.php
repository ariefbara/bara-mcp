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
    
    public function __construct(GenerateConsultantCommentRepliedByParticipantNotification $generateConsultantCommentRepliedByParticipantNotification)
    {
        $this->generateConsultantCommentRepliedByParticipantNotification = $generateConsultantCommentRepliedByParticipantNotification;
    }

    
    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $commentId = $event->getId();
        $this->generateConsultantCommentRepliedByParticipantNotification->execute($commentId);
    }

}
