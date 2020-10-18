<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfClient(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession;

    public function allConsultationsSessionOfClient(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize, ConsultationSessionFilter $consultationSessionFilter);
}
