<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Coordinator;

interface CoordinatorRepository
{
    public function aCoordinatorCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Coordinator;
}
