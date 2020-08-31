<?php

namespace Firm\Application\Service\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession;

interface ConsultationSessionRepository
{

    public function aConsultationSessionOfClient(
            string $firmId, string $clientId, string $programId, string $consultationSessionId): ConsultationSession;
}
