<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;

class ViewAllTask implements ParticipantQueryTask
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
     * @param Participant $participant
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->taskRepository
                ->allTaskForParticipant($participant->getId(), $payload->getFilter());
    }

}
