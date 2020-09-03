<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Application\Service\Participant\ConsultationRequestRepository;

class ClientCancelConcultationRequest
{

    /**
     *
     * @var ConsultationRequestRepository
     */
    protected $consultationRequestRepository;

    function __construct(ConsultationRequestRepository $consultationRequestRepository)
    {
        $this->consultationRequestRepository = $consultationRequestRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $consultationRequestId): void
    {
        $this->consultationRequestRepository
                ->aConsultationRequestFromClientParticipant($firmId, $clientId, $programId, $consultationRequestId)
                ->cancel();
        $this->consultationRequestRepository->update();
    }

}
