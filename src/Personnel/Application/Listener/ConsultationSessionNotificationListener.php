<?php

namespace Personnel\Application\Listener;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSession\PersonnelNotificationOnConsultationSessionAdd;
use Resources\Application\Event\{
    Event,
    Listener
};

class ConsultationSessionNotificationListener implements Listener
{

    /**
     *
     * @var PersonnelNotificationOnConsultationSessionAdd
     */
    protected $personnelNotificationOnConsultationSessionAdd;

    function __construct(PersonnelNotificationOnConsultationSessionAdd $personnelNotificationOnConsultationSessionAdd)
    {
        $this->personnelNotificationOnConsultationSessionAdd = $personnelNotificationOnConsultationSessionAdd;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(EventInterfaceForPersonnelNotification $event): void
    {
        $consultationSessionId = $event->getId();
        $message = $event->getMessageForPersonnel();
        $this->personnelNotificationOnConsultationSessionAdd->execute($consultationSessionId, $message);
    }

}
