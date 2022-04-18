<?php

namespace Client\Domain\Task\Repository\Firm;

use Client\Domain\DependencyModel\Firm\Program;

interface ProgramRepository
{

    public function ofId(string $id): Program;
}
