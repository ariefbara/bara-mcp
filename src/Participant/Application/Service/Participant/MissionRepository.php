<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\DependencyEntity\Firm\Program\Mission;

interface MissionRepository
{

    public function ofId(string $firmId, string $programId, string $missionId): Mission;
}
