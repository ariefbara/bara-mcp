<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function aConsultationSetupInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $consultationSetupId): ConsultationSetup;
}
