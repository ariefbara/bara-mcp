<?php

namespace Personnel\Application\Listener;

use Resources\Application\Event\{
    Event,
    Listener
};

class ParticipantMutateConsultationRequestListener implements Listener
{

    /**
     *
     * @var PersonnelNotificationRepository
     */
    protected $personnelNotificationRepository;

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    function __construct(PersonnelNotificationRepository $personnelNotificationRepository,
            ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->personnelNotificationRepository = $personnelNotificationRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ParticipantMutateConsultationRequestEventInterface $event): void
    {
        $id = $this->personnelNotificationRepository->nextIdentity();
        $personnelNotification = $this->consultationRequestRepository
                ->aConsultationRequestOfParticipant(
                        $event->getClientId(), $event->getParticipantId(), $event->getConsultationRequestId())
                ->createNotification($id, $event->getMessageForPersonnel());
        $this->personnelNotificationRepository->add($personnelNotification);
    }

}
