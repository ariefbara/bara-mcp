<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestBelongsToTeam(string $teamId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsBelongsInProgramParticipationOfTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter);
}
