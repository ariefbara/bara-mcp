<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

interface ConsultationSessionRepository
{
    
    public function update(): void;

    public function aConsultationSessionById(string $consultationSessionId): ConsultationSession;

    public function ofId(ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationSessionId): ConsultationSession;

    public function all(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize);
}
