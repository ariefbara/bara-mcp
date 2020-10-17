<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

class GenerateNotificationWhenConsultationRequestOffered
{
    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;
    
    public function __construct(ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
    }
    
    public function execute(string $consultationRequestId): void
    {
        $this->consultationRequestRepository->ofId($consultationRequestId)
                ->createNotificationTriggeredByConsultant(ConsultationRequest::OFFERED_BY_CONSULTANT);
        $this->consultationRequestRepository->update();
    }

}
