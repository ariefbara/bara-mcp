<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program;

interface ProgramRepository
{

    public function aProgramOfId(string $programId): Program;
}
