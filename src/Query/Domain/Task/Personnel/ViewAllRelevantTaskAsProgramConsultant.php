<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;

class ViewAllRelevantTaskAsProgramConsultant implements PersonnelTask
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
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->taskRepository
                ->allRelevanTaskAsProgrmConsultantOfPersonnel($personnelId, $payload->getFilter());
    }

}
