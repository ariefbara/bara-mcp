<?php

namespace User\Application\Listener;

use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultantMutateConsultationSessionListener implements Listener
{

    /**
     *
     * @var UserNotificationRepository
     */
    protected $userNotificationRepository;

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    function __construct(
            UserNotificationRepository $userNotificationRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->userNotificationRepository = $userNotificationRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantMutateConsultationSessionEventInterface $event): void
    {
        $id = $this->userNotificationRepository->nextIdentity();
        $userNotification = $this->consultationSessionRepository
                ->aConsultationSessionOfConsultant(
                        $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                        $event->getConsultationSessionId())
                ->createUserNotification($id, $event->getmessageForUser());
        $this->userNotificationRepository->add($userNotification);
    }

}
