<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationRequestRejected,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationRequestRejectedListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationRequestRejected
     */
    protected $generateNotificationWhenConsultationRequestRejected;

    public function __construct(
            GenerateNotificationWhenConsultationRequestRejected $generateNotificationWhenConsultationRequestRejected)
    {
        $this->generateNotificationWhenConsultationRequestRejected = $generateNotificationWhenConsultationRequestRejected;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    protected function execute(CommonEvent $event): void
    {
        $consultationRequestId = $event->getId();
        $this->generateNotificationWhenConsultationRequestRejected->execute($consultationRequestId);
    }

}
