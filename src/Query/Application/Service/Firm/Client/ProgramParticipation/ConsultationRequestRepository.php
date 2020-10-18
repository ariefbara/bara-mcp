<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfClient(
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsOfClient(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
