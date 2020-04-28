<?php

namespace Client\Application\Service\Firm\Program;

use Client\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function ofId(string $clientId, string $programParticipationId, string $consultationSetupId): ConsultationSetup;
}
