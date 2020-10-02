<?php

namespace Query\Domain\Service\Firm\Program\Participant;

use Query\{
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsBelongsToTeam(
            string $teamId, string $teamProgramParticipationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter);
}
