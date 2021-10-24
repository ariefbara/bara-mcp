<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\ConsultationSession;

interface ConsultationSessionRepository
{
    public function nextIdentity(): string;
    
    public function add(ConsultationSession $consultationSession): void;
    
    public function ofId(string $consultationSessionId): ConsultationSession;
}
