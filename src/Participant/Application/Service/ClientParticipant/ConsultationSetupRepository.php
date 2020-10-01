<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function aConsultationSetupInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSetupId): ConsultationSetup;
}
