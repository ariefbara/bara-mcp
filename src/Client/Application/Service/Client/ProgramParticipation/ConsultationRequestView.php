<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest;

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

    public function showById(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->ofId($programParticipationCompositionId, $consultationRequestId);
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
        return $this->consultationRequestRepository->all($programParticipationCompositionId, $page, $pageSize);
    }

}
