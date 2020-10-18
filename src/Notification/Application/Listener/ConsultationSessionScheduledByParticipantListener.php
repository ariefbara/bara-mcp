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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationSessionScheduledByParticipant $generateNotificationWhenConsultationSessionScheduledByParticipant,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationSessionScheduledByParticipant = $generateNotificationWhenConsultationSessionScheduledByParticipant;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationSessionId= $event->getId();
        $this->generateNotificationWhenConsultationSessionScheduledByParticipant->execute($consultationSessionId);
    }

}
