<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\Worksheet\Comment\CommentNotificationFromConsultantAdd;
use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultantCommentNotificationListener implements Listener
{

    /**
     *
     * @var CommentNotificationFromConsultantAdd
     */
    protected $commentNotificationFromConsultantAdd;

    function __construct(CommentNotificationFromConsultantAdd $commentNotificationFromConsultantAdd)
    {
        $this->commentNotificationFromConsultantAdd = $commentNotificationFromConsultantAdd;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantCommentNotificationEventInterface $event): void
    {
        $programConsultantCompositionid = new ProgramConsultantCompositionId(
                $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId());
        $consultantCommentId = $event->getConsultantCommentId();
        $message = $event->getMessageForParticipant();

        $this->commentNotificationFromConsultantAdd->execute($programConsultantCompositionid, $consultantCommentId,
                $message);
    }

}
