<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    GenerateNotificationWhenConsultationSessionScheduledByParticipant,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ConsultationSessionScheduledByParticipantListener implements Listener
{

    /**
     *
     * @var GenerateNotificationWhenConsultationSessionScheduledByParticipant
     */
    protected $generateNotificationWhenConsultationSessionScheduledByParticipant;

    public function __construct(
            GenerateNotificationWhenConsultationSessionScheduledByParticipant $generateNotificationWhenConsultationSessionScheduledByParticipant)
    {
        $this->generateNotificationWhenConsultationSessionScheduledByParticipant = $generateNotificationWhenConsultationSessionScheduledByParticipant;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationSessionId= $event->getId();
        $this->generateNotificationWhenConsultationSessionScheduledByParticipant->execute($consultationSessionId);
    }

}
