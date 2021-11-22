<?php

namespace Firm\Domain\Task\Dependency\Firm;

use Firm\Domain\Model\Firm\Program;

interface ProgramRepository
{
    public function aProgramOfId(string $programId): Program;
}
