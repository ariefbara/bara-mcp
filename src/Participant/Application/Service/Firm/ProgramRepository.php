<?php

namespace Participant\Application\Service\Firm;

use Participant\Domain\DependencyModel\Firm\Program;

interface ProgramRepository
{
    public function ofId(string $firmId, string $programId): Program;
}
