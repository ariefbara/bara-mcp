<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Coordinator\CoordinatorNoteRepository;

class ViewAccessibleCoordinatorNote implements ParticipantQueryTask
{

    /**
     * 
     * @var CoordinatorNoteRepository
     */
    protected $coordinatorNoteRepository;

    public function __construct(CoordinatorNoteRepository $coordinatorNoteRepository)
    {
        $this->coordinatorNoteRepository = $coordinatorNoteRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->coordinatorNoteRepository
                ->aCoordinatorNoteAccessibleByParticipant($participant->getId(), $payload->getId());
    }

}
