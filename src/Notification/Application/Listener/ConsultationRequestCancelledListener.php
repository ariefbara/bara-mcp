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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationRequestCancelled $generateNotificationWhenConsultationRequestCancelled,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationRequestCancelled = $generateNotificationWhenConsultationRequestCancelled;
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
        $this->generateNotificationWhenConsultationRequestCancelled->execute($consultationRequestId);
    }

}
