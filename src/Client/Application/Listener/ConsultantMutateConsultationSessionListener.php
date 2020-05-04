<?php

namespace Client\Application\Listener;

use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultantMutateConsultationSessionListener implements Listener
{

    /**
     *
     * @var ClientNotificationRepository
     */
    protected $clientNotificationRepository;

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    function __construct(
            ClientNotificationRepository $clientNotificationRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->clientNotificationRepository = $clientNotificationRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantMutateConsultationSessionEventInterface $event): void
    {
        $id = $this->clientNotificationRepository->nextIdentity();
        $clientNotification = $this->consultationSessionRepository
                ->aConsultationSessionOfConsultant(
                        $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                        $event->getConsultationSessionId())
                ->createClientNotification($id, $event->getmessageForClient());
        $this->clientNotificationRepository->add($clientNotification);
    }

}
