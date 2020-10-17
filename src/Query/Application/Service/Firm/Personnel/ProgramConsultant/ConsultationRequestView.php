<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter as ConsultationRequestFilter2
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

    public function showAll(
            string $personnelId, string $programConsultationId, int $page, int $pageSize,
            ?ConsultationRequestFilter2 $consultationRequestFilter)
    {
        return $this->consultationRequestRepository->allConsultationRequestBelongsToConsultant(
                        $personnelId, $programConsultationId, $page, $pageSize, $consultationRequestFilter);
    }

    public function showById(string $personnelId, string $programConsultationId, string $consultationRequestId): ConsultationRequest
    {
        return $this->consultationRequestRepository->aConsultationRequestBelongsToConsultant(
                $personnelId, $programConsultationId, $consultationRequestId);
    }

}
