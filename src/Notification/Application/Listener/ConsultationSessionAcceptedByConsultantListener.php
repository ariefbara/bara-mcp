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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationSessionAcceptedByConsultant $generateNotificationWhenConsultationSessionAcceptedByConsultant,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationSessionAcceptedByConsultant = $generateNotificationWhenConsultationSessionAcceptedByConsultant;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationSessionId = $event->getId();
        $this->generateNotificationWhenConsultationSessionAcceptedByConsultant->execute($consultationSessionId);
    }

}
