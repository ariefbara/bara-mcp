<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function ofId(ParticipantCompositionId $participantCompositionId, string $consultationSessionId): ConsultationSession;

    public function all(ParticipantCompositionId $participantCompositionId, int $page, int $pageSize);
}
