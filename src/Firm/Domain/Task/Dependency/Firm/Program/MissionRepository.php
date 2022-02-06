<?php

namespace Firm\Domain\Task\Dependency\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function aMissionOfId(string $missionId): Mission;
}
