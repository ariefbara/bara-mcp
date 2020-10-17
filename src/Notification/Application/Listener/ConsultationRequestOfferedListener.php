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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationRequestOffered $generateNotificationWhenConsultationRequestOffered,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationRequestOffered = $generateNotificationWhenConsultationRequestOffered;
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
        $this->generateNotificationWhenConsultationRequestOffered->execute($consultationRequestId);
    }

}
