<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Mission;

interface MissionRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $missionId): Mission;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize, ?string $position = null);
    
}
