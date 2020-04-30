<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationRequest;

use Client\Application\Service\Client\ProgramParticipation\ConsultationRequestRepository;

class ConsultationRequestNotificationAdd
{

    /**
     *
     * @var ConsultationRequestNotificationRepository
     */
    protected $consultationRequestNotificationRepository;

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    function __construct(ConsultationRequestNotificationRepository $consultationRequestNotificationRepository,
            ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->consultationRequestNotificationRepository = $consultationRequestNotificationRepository;
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function execute(string $firmId, string $personnelId, string $consultantId, string $consultationRequestId,
            string $message): void
    {
        $id = $this->consultationRequestNotificationRepository->nextIdentity();
        $consultationRequestNotification = $this->consultationRequestRepository
                ->aConsultationRequestOfConsultant($firmId, $personnelId, $consultantId, $consultationRequestId)
                ->createConsultationRequestNotification($id, $message);
        $this->consultationRequestNotificationRepository->add($consultationRequestNotification);
    }

}
