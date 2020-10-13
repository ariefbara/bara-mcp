<?php

namespace Participant\Application\Service\UserParticipant;

use Participant\Domain\DependencyModel\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $missionId): Mission;

    public function ofId(string $missionId): Mission;
}
