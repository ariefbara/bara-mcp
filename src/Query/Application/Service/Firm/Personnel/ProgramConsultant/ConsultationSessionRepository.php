<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfPersonnel(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsOfPersonnel(
            ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize);
}
