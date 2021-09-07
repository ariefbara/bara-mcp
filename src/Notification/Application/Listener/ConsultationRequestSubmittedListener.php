<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationRequestSubmitted,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationRequestSubmittedListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationRequestSubmitted
     */
    protected $generateNotificationWhenConsultationRequestSubmitted;

    public function __construct(
            GenerateNotificationWhenConsultationRequestSubmitted $generateNotificationWhenConsultationRequestSubmitted)
    {
        $this->generateNotificationWhenConsultationRequestSubmitted = $generateNotificationWhenConsultationRequestSubmitted;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationRequestId = $event->getId();
        $this->generateNotificationWhenConsultationRequestSubmitted->execute($consultationRequestId);
    }

}
