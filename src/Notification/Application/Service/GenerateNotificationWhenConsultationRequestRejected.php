<?php

namespace Notification\Application\Service;

use SharedContext\Domain\ValueObject\MailMessageBuilder;

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
                ->createNotificationTriggeredByConsultant(MailMessageBuilder::CONSULTATION_REJECTED);
        $this->consultationRequestRepository->update();
    }

}
