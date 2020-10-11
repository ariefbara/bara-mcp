<?php

namespace Notification\Application\Service;

class GenerateConsultationRequestNotificationTriggeredByParticipant
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

    public function execute(string $consultationRequestId, int $state): void
    {
        $this->consultationRequestRepository->ofId($consultationRequestId)
                ->createNotificationTriggeredByParticipant($state);
        $this->consultationRequestRepository->update();
    }

}
