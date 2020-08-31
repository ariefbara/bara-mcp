<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function update(): void;

    public function aConsultationSessionOfClientParticipant(string $firmId, string $clientId, string $programId, string $consultationSessionId): ConsultationSession;
}
