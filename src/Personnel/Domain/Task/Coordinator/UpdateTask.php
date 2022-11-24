<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorTaskRepository;

class UpdateTask implements CoordinatorTask
{

    /**
     * 
     * @var CoordinatorTaskRepository
     */
    protected $coordinatorTaskRepository;

    public function __construct(CoordinatorTaskRepository $coordinatorTaskRepository)
    {
        $this->coordinatorTaskRepository = $coordinatorTaskRepository;
    }

    /**
     * 
     * @param Coordinator $coordinator
     * @param UpdateTaskPayload $payload
     * @return void
     */
    public function execute(Coordinator $coordinator, $payload): void
    {
        $coordinatorTask = $this->coordinatorTaskRepository->ofId($payload->getId());
        $coordinatorTask->assertManageableByCoordinator($coordinator);
        $coordinatorTask->update($payload->getLabelData());
    }

}
