<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;

class ExecuteProgramTask
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
            string $firmId, string $personnelId, string $programId, TaskInProgramExecutableByCoordinator $task, $payload): void
    {
        $this->coordinatorRepository->aCoordinatorCorrespondWithProgram($firmId, $personnelId, $programId)
                ->executeProgramTask($task, $payload);
        $this->coordinatorRepository->update();
    }

}
