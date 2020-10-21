<?php

namespace Query\Application\Service\Firm\Program;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ConsultationSessionRepository as InterfaceForClient,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository as InterfaceForPersonnel,
    Application\Service\Firm\Team\ProgramParticipation\ConsultationSessionRepository as InterfaceForTeam,
    Application\Service\User\ProgramParticipation\ConsultationSessionRepository as InterfaceForUser,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository extends InterfaceForClient, InterfaceForPersonnel, InterfaceForTeam, InterfaceForUser
{

    public function aConsultationSessionInProgram(string $programId, string $consultationSessionId): ConsultationSession;

    public function allConsultationSessionsInProgram(
            string $programId, int $page, int $pageSize, ?ConsultationSessionFilter $consultationSessionFilter);
}
