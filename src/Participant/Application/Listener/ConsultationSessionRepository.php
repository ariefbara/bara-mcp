<?php

namespace User\Application\Listener;

use User\Domain\Model\User\ProgramParticipation\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultationSessionId): ConsultationSession;
}
