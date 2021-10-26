<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;

class ExecuteTaskAsProgramCoordinator
{

    /**
     * 
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    public function __construct(CoordinatorRepository $coordinatorRepository)
    {
        $this->coordinatorRepository = $coordinatorRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, ITaskInProgramExecutableByCoordinator $task): void
    {
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->executeTaskInProgram($task);
    }

}
