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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            GenerateNotificationWhenConsultationRequestTimeChanged $generateNotificationWhenConsultationRequestTimeChanged,
            SendImmediateMail $sendImmediateMail)
    {
        $this->generateNotificationWhenConsultationRequestTimeChanged = $generateNotificationWhenConsultationRequestTimeChanged;
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
        $this->generateNotificationWhenConsultationRequestTimeChanged->execute($consultationRequestId);
    }

}
