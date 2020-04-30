<?php

namespace Client\Application\Listener;

use Client\Application\Service\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotificationAdd;
use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultationRequestNotificationLIstener implements Listener
{

    /**
     *
     * @var ConsultationRequestNotificationAdd
     */
    protected $consultationRequestNotificationAdd;

    function __construct(ConsultationRequestNotificationAdd $consultationRequestNotificationAdd)
    {
        $this->consultationRequestNotificationAdd = $consultationRequestNotificationAdd;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ConsultationRequestNotificationEventInterface $event): void
    {
        $this->consultationRequestNotificationAdd->execute(
                $event->getFirmId(), $event->getPersonnelId(), $event->getConsultantId(),
                $event->getConsultationRequestId(), $event->getMessageForClient());
    }

}
