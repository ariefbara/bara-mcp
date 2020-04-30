<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

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
    
    public function showById(ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->ofId($programConsultantCompositionId, $consultationRequestId);
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
        return $this->consultationRequestRepository->all($programConsultantCompositionId, $page, $pageSize);
    }

}
