<?php

namespace Query\Application\Service\Firm\Program\ConsulationSetup;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ConsultationSessionRepository as InterfaceForClient,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationSessionRepository as InterfaceForPersonnel,
    Application\Service\Firm\Team\ProgramParticipation\ConsultationSessionRepository as InterfaceForTeam,
    Application\Service\User\ProgramParticipation\ConsultationSessionRepository as InterfaceForUser,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession,
    Infrastructure\QueryFilter\ConsultationSessionFilter
};

interface ConsultationSessionRepository extends InterfaceForPersonnel, InterfaceForClient, InterfaceForUser, InterfaceForTeam
{

    public function ofId(string $firmId, string $programId, string $consultationSetupId, string $consultationSessionId): ConsultationSession;

    public function all(
            string $firmId, string $programId, string $consultationSetupId, int $page, int $pageSize, 
            ?ConsultationSessionFilter $consultationSessionFilter);
}
