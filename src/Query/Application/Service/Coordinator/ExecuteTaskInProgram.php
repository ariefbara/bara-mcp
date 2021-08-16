<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;

class ExecuteTaskInProgram
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
            string $firmId, string $personnelId, string $coordinatorId, ITaskInProgramExecutableByCoordinator $task): void
    {
        $this->coordinatorRepository
                ->aCoordinatorBelongsToPersonnel($firmId, $personnelId, $coordinatorId)
                ->executeTaskInProgram($task);
    }

}
