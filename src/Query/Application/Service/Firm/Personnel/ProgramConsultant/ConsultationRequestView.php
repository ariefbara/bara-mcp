<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
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
     * @param ProgramConsultantCompositionId $programConsultantCompositionId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationRequest[]
     */
    public function showAll(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize)
    {
        return $this->consultationRequestRepository->allConsultationRequestsOfPersonnel(
                        $programConsultantCompositionId, $page, $pageSize);
    }

    public function showById(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestOfPersonnel($programConsultantCompositionId,
                        $consultationRequestId);
    }

}
