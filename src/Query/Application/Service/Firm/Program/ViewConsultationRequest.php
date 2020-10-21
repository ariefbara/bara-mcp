<?php

namespace Query\Application\Service\Firm\Program;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

class ViewConsultationRequest
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

    /**
     * 
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $programId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository
                        ->allConsultationRequestsInProgram($programId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $programId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestInProgram($programId, $consultationRequestId);
    }

}
