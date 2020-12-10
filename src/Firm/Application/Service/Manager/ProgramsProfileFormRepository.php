<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;

interface ProgramsProfileFormRepository
{
    public function ofId(string $programsProfileFormId): ProgramsProfileForm;
    
    public function update(): void;
}
