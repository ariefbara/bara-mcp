<?php

namespace User\Application\Listener;

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
     * @var UserNotificationRepository
     */
    protected $userNotificationRepository;

    function __construct(
            CommentRepository $commentRepository, UserNotificationRepository $userNotificationRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->userNotificationRepository = $userNotificationRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantCommentOnWorsheetEventInterface $event): void
    {
        $id = $this->userNotificationRepository->nextIdentity();
        $userNotification = $this->commentRepository->aCommentFromConsultant(
                        $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                        $event->getConsultantCommentId())
                ->createUserNotification($id, $event->getMessageForParticipant());
        $this->userNotificationRepository->add($userNotification);
    }

}
