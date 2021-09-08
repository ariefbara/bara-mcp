<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationSessionAcceptedByConsultant,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationSessionAcceptedByConsultantListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationSessionAcceptedByConsultant
     */
    protected $generateNotificationWhenConsultationSessionAcceptedByConsultant;

    public function __construct(
            GenerateNotificationWhenConsultationSessionAcceptedByConsultant $generateNotificationWhenConsultationSessionAcceptedByConsultant)
    {
        $this->generateNotificationWhenConsultationSessionAcceptedByConsultant = $generateNotificationWhenConsultationSessionAcceptedByConsultant;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationSessionId = $event->getId();
        $this->generateNotificationWhenConsultationSessionAcceptedByConsultant->execute($consultationSessionId);
    }

}
