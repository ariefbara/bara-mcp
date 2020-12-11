<?php

namespace Participant\Application\Service;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;

interface ProgramsProfileFormRepository
{

    public function ofId(string $programsProfileFormId): ProgramsProfileForm;
}
