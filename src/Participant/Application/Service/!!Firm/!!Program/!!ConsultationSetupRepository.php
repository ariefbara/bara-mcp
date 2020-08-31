<?php

namespace User\Application\Service\Firm\Program;

use User\Domain\Model\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function aConsultationSetupInProgramWhereUserParticipate(string $userId, string $programParticipationId, string $consultationSetupId): ConsultationSetup;
}
