<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $missionId): Mission;
    
    public function aMissionByPositionInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $missionPosition): Mission;

    public function allMissionsInProgramWhereUserParticipate(string $userId, string $programParticipationId);
}
