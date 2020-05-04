<?php

namespace Personnel\Application\Listener;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfParticipant(
            string $clientId, string $participantId, string $consultationSessionId): ConsultationSession;
}
