<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\ProgramsProfileForm;

interface ProgramsProfileFormRepository
{

    public function aProgramsProfileFormInProgram(string $firmId, string $programId, string $programsProfileFormId): ProgramsProfileForm;

    public function allProgramsProfileFormsInProgram(
            string $firmId, string $programId, int $page, int $pageSize, ?bool $enableOnly = true);
}
