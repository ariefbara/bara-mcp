<?php

namespace Personnel\Application\Listener;

use Resources\Application\Event\{
    Event,
    Listener
};

class ParticipantMutateConsultationSessionListener implements Listener
{

    /**
     *
     * @var PersonnelNotificationRepository
     */
    protected $personnelNotificationRepository;

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;

    function __construct(PersonnelNotificationRepository $personnelNotificationRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->personnelNotificationRepository = $personnelNotificationRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ParticipantMutateConsultationSessionEventInterface $event): void
    {
        $id = $this->personnelNotificationRepository->nextIdentity();
        $personnelNotification = $this->consultationSessionRepository
                ->aConsultationSessionOfParticipant(
                        $event->getClientId(), $event->getParticipantId(), $event->getConsultationSessionId())
                ->createNotification($id, $event->getMessageForPersonnel());
        $this->personnelNotificationRepository->add($personnelNotification);
    }

}
