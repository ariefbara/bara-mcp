<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfPersonnel(
            string $firmId, string $personnelId, string $programId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsOfPersonnel(
            string $firmId, string $personnelId, string $programId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
