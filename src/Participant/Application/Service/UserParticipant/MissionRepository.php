<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $missionId): Mission;
}
