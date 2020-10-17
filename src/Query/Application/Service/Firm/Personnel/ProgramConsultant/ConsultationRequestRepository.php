<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestBelongsToConsultant(
            string $personnelId, string $programConsultationId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestBelongsToConsultant(
            string $personnelId, string $programConsultationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
