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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationRequestSubmitted $generateNotificationWhenConsultationRequestSubmitted,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationRequestSubmitted = $generateNotificationWhenConsultationRequestSubmitted;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $consultationRequestId = $event->getId();
        $this->generateNotificationWhenConsultationRequestSubmitted->execute($consultationRequestId);
    }

}
