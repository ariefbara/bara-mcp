<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Application\Service\Participant\ConsultationRequestRepository;

class UserParticipantCancelConcultationRequest
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
            string $userId, string $userParticipantId, string $consultationRequestId): void
    {
        $this->consultationRequestRepository
                ->aConsultationRequestFromUserParticipant($userId, $userParticipantId, $consultationRequestId)
                ->cancel();
        $this->consultationRequestRepository->update();
    }
}
