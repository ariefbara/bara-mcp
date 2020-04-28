<?php

namespace Client\Application\Service\Firm\Program;

use Client\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function ofId(string $clientId, string $programParticipationId, string $missionId): Mission;
}
