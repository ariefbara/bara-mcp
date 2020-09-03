<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $consultantId): Consultant;
}
