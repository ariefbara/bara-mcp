<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\{
    GenerateNotificationWhenConsultationRequestOffered,
    SendImmediateMail
};
use Resources\{
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationRequestOfferedListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationRequestOffered
     */
    protected $generateNotificationWhenConsultationRequestOffered;

    public function __construct(
            GenerateNotificationWhenConsultationRequestOffered $generateNotificationWhenConsultationRequestOffered)
    {
        $this->generateNotificationWhenConsultationRequestOffered = $generateNotificationWhenConsultationRequestOffered;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $consultationRequestId = $event->getId();
        $this->generateNotificationWhenConsultationRequestOffered->execute($consultationRequestId);
    }

}
