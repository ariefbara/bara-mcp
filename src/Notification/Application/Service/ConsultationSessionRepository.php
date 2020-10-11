<?php

namespace Notification\Application\Service;

use Notification\Domain\Model\Firm\Program\Participant\ConsultationSession;

interface ConsultationSessionRepository
{

    public function ofId(string $consultationSessionId): ConsultationSession;

    public function update(): void;
}
