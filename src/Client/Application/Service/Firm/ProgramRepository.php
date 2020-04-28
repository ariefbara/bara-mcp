<?php

namespace Client\Application\Service\Firm;

use Client\Domain\Model\Firm\Program;

interface ProgramRepository
{
    public function ofId(string $firmId, string $programId): Program;
}
