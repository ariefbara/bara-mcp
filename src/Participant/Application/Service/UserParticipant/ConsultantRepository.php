<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\Consultant;

interface ConsultantRepository
{

    public function aConsultantInProgramWhereUserParticipate(
            string $userId, string $userParticipantId, string $consultantId): Consultant;
}
