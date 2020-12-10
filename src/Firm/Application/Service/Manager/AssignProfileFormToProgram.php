<?php

namespace Firm\Application\Service\Manager;

class AssignProfileFormToProgram
{

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var ProfileFormRepository
     */
    protected $profileFormRepository;

    function __construct(ProgramRepository $programRepository, ManagerRepository $managerRepository,
            ProfileFormRepository $profileFormRepository)
    {
        $this->programRepository = $programRepository;
        $this->managerRepository = $managerRepository;
        $this->profileFormRepository = $profileFormRepository;
    }

    public function execute(string $firmId, string $managerId, string $programId, string $profileFormId): string
    {
        $program = $this->programRepository->aProgramOfId($programId);
        $profileForm = $this->profileFormRepository->ofId($profileFormId);
        
        $programsProfileFormId = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->assignProfileFormToProgram($program, $profileForm);
        
        $this->programRepository->update();
        
        return $programsProfileFormId;
    }

}
