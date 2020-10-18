<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository
{

    public function aConsultationSessionFromUserParticipant(
            string $userId, string $userParticipantId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionFromUserParticipant(
            string $userId, string $userParticipantId, int $page, int $pageSize,
            ?ConsultationSessionFilter $consultationSessionFilter);
}
