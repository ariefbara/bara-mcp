<?php

namespace Client\Application\Service\Client;

use SharedContext\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function ofId(string $firmId, string $programId): Program;
}
