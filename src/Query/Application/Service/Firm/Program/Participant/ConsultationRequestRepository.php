<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function ofId(ParticipantCompositionId $participantCompositionId, string $consultationRequestId): ConsultationRequest;

    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize);
}
