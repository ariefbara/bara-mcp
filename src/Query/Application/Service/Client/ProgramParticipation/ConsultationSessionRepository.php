<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfClient(
            string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsOfClient(
            string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter);
}
