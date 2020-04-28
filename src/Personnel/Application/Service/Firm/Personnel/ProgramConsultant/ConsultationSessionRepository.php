<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionById(string $consultationSessionId): ConsultationSession;
}
