<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotificationAdd;
use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultationSessionNotificationListener implements Listener
{

    /**
     *
     * @var ConsultationSessionNotificationAdd
     */
    protected $consultationSessionNotificationAdd;

    function __construct(ConsultationSessionNotificationAdd $consultationSessionNotificationAdd)
    {
        $this->consultationSessionNotificationAdd = $consultationSessionNotificationAdd;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultationSessionNotificationEventInterface $event): void
    {
        $this->consultationSessionNotificationAdd->execute(
                $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                $event->getConsultationSessionId(), $event->getmessageForClient());
    }

}
