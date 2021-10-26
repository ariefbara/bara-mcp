<?php

namespace Participant\Domain\Task\Dependency\Firm\Program;

use Participant\Domain\DependencyModel\Firm\Program\Consultant;

interface MentorRepository
{

    public function ofId(string $consultantId): Consultant;
}
