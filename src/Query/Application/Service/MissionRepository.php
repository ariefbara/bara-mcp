<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function allPublishedMissionInProgram(string $programId);

    public function aPublishedMission(string $id): Mission;
}
