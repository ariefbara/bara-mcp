<?php

namespace Firm\Application\Service\Manager;

class DisableProgramsProfileForm
{

    /**
     * 
     * @var ProgramsProfileFormRepository
     */
    protected $programsProfileFormRepository;

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;
    
    function __construct(ProgramsProfileFormRepository $programsProfileFormRepository,
            ManagerRepository $managerRepository)
    {
        $this->programsProfileFormRepository = $programsProfileFormRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $programsProfileFormId): void
    {
        $programsProfileForm = $this->programsProfileFormRepository->ofId($programsProfileFormId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableProgramsProfileForm($programsProfileForm);
        $this->programsProfileFormRepository->update();
    }


}
