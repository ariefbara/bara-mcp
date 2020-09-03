<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\ConsultationSetup;

interface ConsultationSetupRepository
{

    public function aConsultationSetupInProgramWhereUserParticipate(
            string $userId, string $userParticipantId, string $consultationSetupId): ConsultationSetup;
}
