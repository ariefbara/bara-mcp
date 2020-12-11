<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\ProgramsProfileForm;

class ViewProgramsProfileForm
{

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    function __construct(ProgramsProfileFormRepository $programsProfileFormRepository)
    {
        $this->programsProfileFormRepository = $programsProfileFormRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return ProgramsProfileForm[]
     */
    public function showAll(string $firmId, string $programId, int $page, int $pageSize, ?bool $enableOnly = true)
    {
        return $this->programsProfileFormRepository
                        ->allProgramsProfileFormsInProgram($firmId, $programId, $page, $pageSize, $enableOnly);
    }

    public function showById(string $firmId, string $programId, string $programsProfileFormId): ProgramsProfileForm
    {
        return $this->programsProfileFormRepository
                        ->aProgramsProfileFormInProgram($firmId, $programId, $programsProfileFormId);
    }

}
