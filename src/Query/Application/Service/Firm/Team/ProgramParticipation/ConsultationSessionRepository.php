<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionBelongsToTeam(string $teamId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsInProgramParticipationOfTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter);
}
