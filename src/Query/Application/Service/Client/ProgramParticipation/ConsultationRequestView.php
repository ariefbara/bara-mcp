<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
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

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationRequest[]
     */
    public function showAll(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        return $this->consultationRequestRepository->allConsultationRequestsOfParticipant(
                        $programParticipationCompositionId, $page, $pageSize);
    }

    public function showById(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestOfParticipant(
                        $programParticipationCompositionId, $consultationRequestId);
    }

}
