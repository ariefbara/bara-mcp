<?php

namespace Client\Application\Service\Firm\Program;

use Client\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function aConsultationSetupInProgramWhereClientParticipate(string $clientId, string $programParticipationId, string $consultationSetupId): ConsultationSetup;
}
