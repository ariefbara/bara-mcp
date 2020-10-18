<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\ {
    Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest,
    Infrastructure\QueryFilter\ConsultationRequestFilter
};

interface ConsultationRequestRepository
{

    public function aConsultationRequestFromUserParticipant(
            string $userId, string $userParticipantId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestFromUserParticipant(
            string $userId, string $userParticipantId, int $page, int $pageSize,
            ?ConsultationRequestFilter $consultationRequestFilter);
}
