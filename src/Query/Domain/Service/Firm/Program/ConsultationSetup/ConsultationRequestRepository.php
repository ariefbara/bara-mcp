<?php

namespace Query\Domain\Service\Firm\Program\ConsultationSetup;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
