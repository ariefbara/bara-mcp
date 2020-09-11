<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $missionId): Mission;

    public function aMissionByPositionInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $missionPosition): Mission;

    public function allMissionsInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId);
}
