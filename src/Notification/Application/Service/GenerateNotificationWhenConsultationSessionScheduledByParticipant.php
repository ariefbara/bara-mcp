<?php

namespace Notification\Application\Service;

class GenerateNotificationWhenConsultationSessionScheduledByParticipant
{
    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;
    
    public function __construct(ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionRepository = $consultationSessionRepository;
    }
    
    public function execute(string $consultationSessionId): void
    {
        $this->consultationSessionRepository->ofId($consultationSessionId)
                ->addAcceptNotificationTriggeredByParticipant();
        $this->consultationSessionRepository->update();
    }

}
