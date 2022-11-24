<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorTaskRepository;

class ViewCoordinatorTaskDetail implements ParticipantQueryTask
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
     * @param Participant $participant
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->coordinatorTaskRepository
                ->aCoordinatorTaskDetailForParticipant($participant->getId(), $payload->getId());
    }

}
