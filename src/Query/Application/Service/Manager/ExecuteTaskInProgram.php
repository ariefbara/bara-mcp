<?php

namespace Query\Application\Service\Manager;

use Query\Domain\Model\Firm\ITaskInProgramExecutableByManager;

class ExecuteTaskInProgram
{

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(ManagerRepository $managerRepository, ProgramRepository $programRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->programRepository = $programRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $programId, ITaskInProgramExecutableByManager $task): void
    {
        $program = $this->programRepository->aProgramOfId($programId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->executeTaskInProgram($program, $task);
    }

}
