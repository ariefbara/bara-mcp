<?php

namespace Firm\Application\Service\Personnel\ProgramCoordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;

interface ProgramCoordinatorRepository
{

    public function aCoordinatorCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Coordinator;
}
