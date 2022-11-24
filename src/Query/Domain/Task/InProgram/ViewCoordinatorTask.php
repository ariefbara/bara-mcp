<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorTaskRepository;

class ViewCoordinatorTask implements ProgramTaskExecutableByCoordinator, ProgramTaskExecutableByConsultant
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
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->coordinatorTaskRepository->aCoordinatorTaskInProgram($programId, $payload->getId());
    }

}
