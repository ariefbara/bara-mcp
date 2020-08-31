<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\ {
    Application\Service\Firm\Program\ConsulationSetup\ConsultationSessionFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfPersonnel(
            string $firmId, string $personnelId, string $programId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsOfPersonnel(
            string $firmId, string $personnelId, string $programId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter);
}
