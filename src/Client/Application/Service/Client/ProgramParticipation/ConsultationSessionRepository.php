<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationSession;

interface ConsultationSessionRepository
{
    
    public function update(): void;

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationSessionId): ConsultationSession;

    public function all(ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize);
}
