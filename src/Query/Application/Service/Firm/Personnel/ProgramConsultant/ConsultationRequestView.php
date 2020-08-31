<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\{
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};

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
     * @param string $firmId
     * @param string $personnelId
     * @param string $consultantId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestsOfPersonnel($firmId, $personnelId,
                        $consultantId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $firmId, string $personnelId, string $consultantId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestOfPersonnel($firmId, $personnelId,
                        $consultantId, $consultationRequestId);
    }

}
