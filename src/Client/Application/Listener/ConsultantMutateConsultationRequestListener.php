<?php

namespace Client\Application\Listener;

use Resources\Application\Event\ {
    Event,
    Listener
};

class ConsultantMutateConsultationRequestListener implements Listener
{

    /**
     *
     * @var ClientNotificationRepository
     */
    protected $clientNotificationRepository;

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    function __construct(
            ClientNotificationRepository $clientNotificationRepository,
            ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->clientNotificationRepository = $clientNotificationRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultantMutateConsultationRequestEventInterface $event): void
    {
        $id = $this->clientNotificationRepository->nextIdentity();
        $clientNotification = $this->consultationRequestRepository
                ->aConsultationRequestOfConsultant(
                        $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                        $event->getConsultationRequestId())
                ->createClientNotification($id, $event->getMessageForClient());
        $this->clientNotificationRepository->add($clientNotification);
    }

}
