<?php

namespace Firm\Application\Listener\Firm\Program\ConsultationSetup;

use Firm\Application\Service\Firm\Program\ConsultationSetup\SendClientConsultationSessionMail;
use Resources\Application\Event\ {
    Event,
    Listener
};

class ClientAcceptedConsultationRequestListener implements Listener
{
    /**
     *
     * @var SendClientConsultationSessionMail
     */
    protected $sendClientConsultationSessionMail;
    
    public function __construct(SendClientConsultationSessionMail $sendClientConsultationSessionMail)
    {
        $this->sendClientConsultationSessionMail = $sendClientConsultationSessionMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(ClientAcceptedConsultationRequestEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $clientId = $event->getClientId();
        $programId = $event->getProgramId();
        $consultationSessionId = $event->getConsultationSessionId();
        
        $this->sendClientConsultationSessionMail->execute($firmId, $clientId, $programId, $consultationSessionId);
    }

}
