<?php

namespace Firm\Application\Listener\Firm\Program\ConsultationSetup;

use Firm\Application\Service\Firm\Program\ConsultationSetup\SendClientConsultationRequestMail;
use Resources\Application\Event\ {
    Event,
    Listener
};

class ClientUpdatedConsultationRequestListener implements Listener
{
    /**
     *
     * @var SendClientConsultationRequestMail
     */
    protected $sendClientConsultationRequestMail;
    
    public function __construct(SendClientConsultationRequestMail $sendClientConsultationRequestMail)
    {
        $this->sendClientConsultationRequestMail = $sendClientConsultationRequestMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    public function execute(ClientUpdatedConsultationRequestEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $clientId = $event->getClientId();
        $programId = $event->getProgramId();
        $consultationRequestId = $event->getConsultationRequestId();
        
        $this->sendClientConsultationRequestMail->execute($firmId, $clientId, $programId, $consultationRequestId);
    }

}
