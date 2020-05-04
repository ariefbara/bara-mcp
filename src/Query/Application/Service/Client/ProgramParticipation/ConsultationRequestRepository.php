<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsOfParticipant(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize);
}
