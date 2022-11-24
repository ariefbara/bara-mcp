<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;

class ViewAllTask implements ProgramTask
{

    /**
     * 
     * @var TaskRepository
     */
    protected $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->taskRepository->allTaskInProgram($programId, $payload->getFilter());
    }

}
