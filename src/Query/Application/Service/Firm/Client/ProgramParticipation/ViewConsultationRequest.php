<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\{
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
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
     * @param string $firmId
     * @param string $clientId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestsOfClient(
                        $firmId, $clientId, $programId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $firmId, string $clientId, string $programId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository
                        ->aConsultationRequestOfClient($firmId, $clientId, $programId, $consultationRequestId);
    }

}
