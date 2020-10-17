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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationRequestRejected $generateNotificationWhenConsultationRequestRejected,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationRequestRejected = $generateNotificationWhenConsultationRequestRejected;
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
        $this->generateNotificationWhenConsultationRequestRejected->execute($consultationRequestId);
    }

}
