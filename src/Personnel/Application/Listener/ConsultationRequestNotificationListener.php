<?php

namespace Personnel\Application\Listener;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequest\PersonnelNotificationOnConsultationRequestAdd;
use Resources\Application\Event\ {
    Event,
    Listener
};

class ConsultationRequestNotificationListener implements Listener
{
    
    /**
     *
     * @var PersonnelNotificationOnConsultationRequestAdd
     */
    protected $personnelNotificationOnConsultationRequestAdd;
    
    function __construct(PersonnelNotificationOnConsultationRequestAdd $personnelNotificationOnConsultationRequestAdd)
    {
        $this->personnelNotificationOnConsultationRequestAdd = $personnelNotificationOnConsultationRequestAdd;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(EventInterfaceForPersonnelNotification $event): void
    {
        $consultationRequestId = $event->getId();
        $message = $event->getMessageForPersonnel();
        $this->personnelNotificationOnConsultationRequestAdd->execute($consultationRequestId, $message);
    }

}
