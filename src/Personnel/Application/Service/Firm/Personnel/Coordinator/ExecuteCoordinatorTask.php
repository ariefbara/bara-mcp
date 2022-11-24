<?php

namespace Personnel\Application\Service\Firm\Personnel\Coordinator;

use Personnel\Domain\Task\Coordinator\CoordinatorTask;

class ExecuteCoordinatorTask
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
    
    public function execute(string $personnelId, string $coordinatorId, CoordinatorTask $task, $payload): void
    {
        $this->coordinatorRepository->aCoordinatorBelongsToPersonnel($personnelId, $coordinatorId)
                ->executeTask($task, $payload);
        $this->coordinatorRepository->update();
    }

}
