<?php

namespace Client\Domain\Task\Repository\Firm\Program;

use Client\Domain\DependencyModel\Firm\Program\Registrant;

interface RegistrantRepository
{

    public function ofId(string $id): Registrant;
}
