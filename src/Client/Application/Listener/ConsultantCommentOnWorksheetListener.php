<?php

namespace Client\Application\Listener;

use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultantCommentOnWorksheetListener implements Listener
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var ClientNotificationRepository
     */
    protected $clientNotificationRepository;

    function __construct(
            CommentRepository $commentRepository, ClientNotificationRepository $clientNotificationRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->clientNotificationRepository = $clientNotificationRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantCommentOnWorsheetEventInterface $event): void
    {
        $id = $this->clientNotificationRepository->nextIdentity();
        $clientNotification = $this->commentRepository->aCommentFromConsultant(
                        $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                        $event->getConsultantCommentId())
                ->createClientNotification($id, $event->getMessageForParticipant());
        $this->clientNotificationRepository->add($clientNotification);
    }

}
