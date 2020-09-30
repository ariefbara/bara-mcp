<?php

namespace Participant\Application\Service\ClientParticipant;

use Participant\Domain\DependencyModel\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionInProgramWhereClientParticipate(
            string $firmId, string $clientId, string $programParticipationId, string $missionId): Mission;
}
