<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Query\{
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\Participant\ConsultationRequest
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfClient(
            string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsOfClient(
            string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
