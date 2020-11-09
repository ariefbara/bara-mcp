<?php

namespace ActivityCreator\Application\Service\Manager;

use ActivityCreator\Domain\DependencyModel\Firm\Program;

interface ProgramRepository
{

    public function ofId(string $programId): Program;
}
