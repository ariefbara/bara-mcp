<?php

namespace User\Application\Service\Firm\Program;

use User\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $consultantId): Consultant;
}
