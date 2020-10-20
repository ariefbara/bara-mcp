<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function ofId(string $firmId, string $programId, string $missionId): Mission;

    public function all(string $firmId, string $programId, int $page, int $pageSize);

    public function aMissionByPositionBelongsToProgram(string $programId, string $position): Mission;
}
