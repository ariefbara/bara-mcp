<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationRequestTimeChanged,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationRequestTimeChangedListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationRequestTimeChanged
     */
    protected $generateNotificationWhenConsultationRequestTimeChanged;

    public function __construct(
            GenerateNotificationWhenConsultationRequestTimeChanged $generateNotificationWhenConsultationRequestTimeChanged)
    {
        $this->generateNotificationWhenConsultationRequestTimeChanged = $generateNotificationWhenConsultationRequestTimeChanged;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationRequestId = $event->getId();
        $this->generateNotificationWhenConsultationRequestTimeChanged->execute($consultationRequestId);
    }

}
