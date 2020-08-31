<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Query\ {
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
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter|null $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestsOfClient(
                        $clientId, $programParticipationId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestOfClient($clientId, $programParticipationId,
                        $consultationRequestId);
    }

}
