<?php

namespace Notification\Application\Service;

class GenerateNotificationWhenConsultationSessionAcceptedByConsultant
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
                ->addAcceptNotificationTriggeredByConsultant();
        $this->consultationSessionRepository->update();
    }

}
