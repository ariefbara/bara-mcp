<?php

namespace Client\Application\Listener;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultationSessionId): ConsultationSession;
}
