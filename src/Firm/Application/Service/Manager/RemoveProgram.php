<?php

namespace Firm\Application\Service\Manager;

class RemoveProgram
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

    function __construct(ProgramRepository $programRepository, ManagerRepository $managerRepository)
    {
        $this->programRepository = $programRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $programId): void
    {
        $program = $this->programRepository->aProgramOfId($programId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)->removeProgram($program);
        $this->programRepository->update();
    }

}
