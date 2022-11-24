<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorTaskRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class SubmitTask implements CoordinatorTask
{

    /**
     * 
     * @var CoordinatorTaskRepository
     */
    protected $coordinatorTaskRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(CoordinatorTaskRepository $coordinatorTaskRepository,
            ParticipantRepository $participantRepository)
    {
        $this->coordinatorTaskRepository = $coordinatorTaskRepository;
        $this->participantRepository = $participantRepository;
    }
    
    /**
     * 
     * @param Coordinator $coordinator
     * @param SubmitTaskPayload $payload
     * @return void
     */
    public function execute(Coordinator $coordinator, $payload): void
    {
        $payload->submittedTaskId = $this->coordinatorTaskRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($payload->getParticipantId());
        $data = $payload->getLabelData();
        $coordinatorTask = $coordinator->submitTask($payload->submittedTaskId, $participant, $data);
        $this->coordinatorTaskRepository->add($coordinatorTask);
    }

}
