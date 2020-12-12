<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

interface ConsultationSessionRepository
{
    public function ofId(string $consultationSessionId): ConsultationSession;
    
    public function update(): void;
}
