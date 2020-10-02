<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession;

    public function aConsultationSessionOfUserParticipant(
            string $userId, string $programParticipationId, string $consultationSessionId): ConsultationSession;
    
    public function ofId(string $consultationSessionId): ConsultationSession;

    public function update(): void;
}
