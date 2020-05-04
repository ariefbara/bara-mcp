<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Coordinator;

interface CoordinatorRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $coordinatorId): Coordinator;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
