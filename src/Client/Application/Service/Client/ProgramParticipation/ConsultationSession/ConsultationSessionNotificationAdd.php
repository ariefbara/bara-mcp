<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationSession;

use Client\Application\Service\Client\ProgramParticipation\ConsultationSessionRepository;

class ConsultationSessionNotificationAdd
{

    /**
     *
     * @var ConsultationSessionNotificationRepository
     */
    protected $consultationSessionNotificationRepository;

    /**
     *
     * @var ConsultationSessionRepository
     */
    protected $consultationSessionRepository;
    
    function __construct(ConsultationSessionNotificationRepository $consultationSesionNotificationRepository,
            ConsultationSessionRepository $consultationSessionRepository)
    {
        $this->consultationSessionNotificationRepository = $consultationSesionNotificationRepository;
        $this->consultationSessionRepository = $consultationSessionRepository;
    }
    
    public function execute(string $firmId, string $personnelId, string $consultantId, string $consultationSessionId, string $message): void
    {
        $id = $this->consultationSessionNotificationRepository->nextIdentity();
        $consultationSessionNotification = $this->consultationSessionRepository
                ->aConsultationSessionOfConsultant($firmId, $personnelId, $consultantId, $consultationSessionId)
                ->createConsultationSessionNotification($id, $message);
        $this->consultationSessionNotificationRepository->add($consultationSessionNotification);
    }


}
