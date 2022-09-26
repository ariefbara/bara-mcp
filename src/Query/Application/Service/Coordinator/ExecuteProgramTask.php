<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;

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
            string $firmId, string $personnelId, string $coordinatorId, ProgramTaskExecutableByCoordinator $task,
            $payload): void
    {
        $this->coordinatorRepository->aCoordinatorBelongsToPersonnel($firmId, $personnelId, $coordinatorId)
                ->executeProgramTask($task, $payload);
    }

}
