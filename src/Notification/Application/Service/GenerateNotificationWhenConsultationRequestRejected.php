<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

class GenerateNotificationWhenConsultationRequestRejected
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
                ->createNotificationTriggeredByConsultant(ConsultationRequest::REJECTED_BY_CONSULTANT);
        $this->consultationRequestRepository->update();
    }

}
