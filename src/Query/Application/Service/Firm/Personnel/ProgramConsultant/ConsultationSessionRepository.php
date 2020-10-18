<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfPersonnel(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsOfPersonnel(
            string $firmId, string $personnelId, string $programConsultationId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter);
}
