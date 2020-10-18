<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationRequest;

class GenerateNotificationWhenConsultationRequestTimeChanged
{
    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consulationRequestRepository;
    
    public function __construct(ConsultationRequestRepository $consulationRequestRepository)
    {
        $this->consulationRequestRepository = $consulationRequestRepository;
    }
    
    public function execute(string $consultationRequestId): void
    {
        $this->consulationRequestRepository->ofId($consultationRequestId)
                ->createNotificationTriggeredByParticipant(ConsultationRequest::TIME_CHANGED_BY_PARTICIPANT);
        $this->consulationRequestRepository->update();
    }

}
