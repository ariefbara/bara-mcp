<?php

namespace Participant\Application\Service\Participant;

use SharedContext\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function ofId(string $firmId, string $programId, string $consultationSetupId): ConsultationSetup;
}
