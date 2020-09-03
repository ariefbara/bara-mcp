<?php

namespace Notification\Application\Service\Firm\Program\ConsultationSetup;

use Notification\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSessionId): ConsultationSession;

    public function aConsultationSessionOfUserParticipant(
            string $userId, string $programParticipationId, string $consultationSessionId): ConsultationSession;

    public function aConsultationSessionOfConsultant(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationSessionId): ConsultationSession;
}
