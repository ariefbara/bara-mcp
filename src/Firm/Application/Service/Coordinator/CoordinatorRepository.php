<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;

interface CoordinatorRepository
{
    public function aCoordinatorCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Coordinator;
}
