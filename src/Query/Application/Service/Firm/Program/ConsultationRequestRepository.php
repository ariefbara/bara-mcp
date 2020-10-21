<?php

namespace Query\Application\Service\Firm\Program;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\ConsultationRequestRepository as InterfaceForClient,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultationRequestRepository as InterfaceForPersonnel,
    Application\Service\Firm\Team\ProgramParticipation\ConsultationRequestRepository as InterfaceForTeam,
    Application\Service\User\ProgramParticipation\ConsultationRequestRepository as InterfaceForUser,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository extends InterfaceForClient, InterfaceForTeam, InterfaceForUser, InterfaceForPersonnel
{

    public function aConsultationRequestInProgram(string $programId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsInProgram(
            string $programId, int $page, int $pageSize, ?ConsultationRequestFilter $consultationRequestFilter);
}
