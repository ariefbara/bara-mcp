<?php

namespace User\Application\Service\User;

use SharedContext\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function ofId(string $firmId, string $programId): Program;
}
