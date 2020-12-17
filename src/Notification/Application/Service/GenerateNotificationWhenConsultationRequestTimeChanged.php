<?php

namespace Notification\Application\Service;

use SharedContext\Domain\ValueObject\MailMessageBuilder;

class GenerateNotificationWhenConsultationRequestTimeChanged
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
                ->createNotificationTriggeredByParticipant(MailMessageBuilder::CONSULTATION_SCHEDULE_CHANGED);
        $this->consultationRequestRepository->update();
    }

}
