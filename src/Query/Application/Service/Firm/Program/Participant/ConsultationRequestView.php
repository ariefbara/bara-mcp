<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ConsultationRequest;

class ConsultationRequestView
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

    public function showById(ParticipantCompositionId $participantCompositionId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->ofId($participantCompositionId, $consultationRequestId);
    }

    /**
     * 
     * @param ParticipantCompositionId $participantCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationRequest[]
     */
    public function showAll(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize)
    {
        return $this->consultationRequestRepository->all($participantCompositionId, $page, $pageSize);
    }

}
