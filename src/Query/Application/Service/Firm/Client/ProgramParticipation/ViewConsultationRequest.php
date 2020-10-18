<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\ {
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
     * @param string $firmId
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestsOfClient(
                        $firmId, $clientId, $programParticipationId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository
                        ->aConsultationRequestOfClient($firmId, $clientId, $programParticipationId, $consultationRequestId);
    }

}
