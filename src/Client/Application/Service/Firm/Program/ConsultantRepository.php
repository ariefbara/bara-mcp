<?php

namespace Client\Application\Service\Firm\Program;

use Client\Domain\Model\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantInProgramWhereClientParticipate(
            string $clientId, string $programParticipationId, string $consultantId): Consultant;
}
