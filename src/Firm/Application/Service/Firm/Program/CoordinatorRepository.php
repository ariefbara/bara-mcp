<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Coordinator;

interface CoordinatorRepository
{

    public function update(): void;

    public function ofId(ProgramCompositionId $programCompositionId, string $coordinatorId): Coordinator;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
