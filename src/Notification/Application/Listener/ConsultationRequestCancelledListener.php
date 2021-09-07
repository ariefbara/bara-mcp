<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationRequestCancelled,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationRequestCancelledListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationRequestCancelled
     */
    protected $generateNotificationWhenConsultationRequestCancelled;

    public function __construct(
            GenerateNotificationWhenConsultationRequestCancelled $generateNotificationWhenConsultationRequestCancelled)
    {
        $this->generateNotificationWhenConsultationRequestCancelled = $generateNotificationWhenConsultationRequestCancelled;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationRequestId = $event->getId();
        $this->generateNotificationWhenConsultationRequestCancelled->execute($consultationRequestId);
    }

}
