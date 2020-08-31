<?php

namespace Query\Application\Service\Firm\Program\ConsulationSetup;

use Query\Domain\Model\Firm\Program\Participant\ConsultationRequest;

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
     * @param string $programId
     * @param string $consultationSetupId
     * @param int $page
     * @param int $pageSize
     * @param ConsultationRequestFilter $consultationRequestFilter
     * @return ConsultationRequest[]
     */
    public function showAll(
            string $firmId, string $programId, string $consultationSetupId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->all(
                        $firmId, $programId, $consultationSetupId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(
            string $firmId, string $programId, string $consultationSetupId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->ofId(
                        $firmId, $programId, $consultationSetupId, $consultationRequestId);
    }

}
