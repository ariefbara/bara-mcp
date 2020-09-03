<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\{
    Application\Service\Firm\Program\ConsulationSetup\ConsultationRequestFilter,
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestFromUserParticipant(
            string $userId, string $userParticipantId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestFromUserParticipant(
            string $userId, string $userParticipantId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
