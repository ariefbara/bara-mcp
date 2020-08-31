<?php

namespace User\Application\Listener;

use Resources\Application\Event\ {
    Event,
    Listener
};

class ConsultantMutateConsultationRequestListener implements Listener
{

    /**
     *
     * @var UserNotificationRepository
     */
    protected $userNotificationRepository;

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    function __construct(
            UserNotificationRepository $userNotificationRepository,
            ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantMutateConsultationRequestEventInterface $event): void
    {
        $id = $this->userNotificationRepository->nextIdentity();
        $userNotification = $this->consultationRequestRepository
                ->aConsultationRequestOfConsultant(
                        $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                        $event->getConsultationRequestId())
                ->createUserNotification($id, $event->getMessageForUser());
        $this->userNotificationRepository->add($userNotification);
    }

}
