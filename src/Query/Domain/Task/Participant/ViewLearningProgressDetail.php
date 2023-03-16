<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\LearningProgressRepository;

class ViewLearningProgressDetail implements ParticipantQueryTask
{

    /**
     * 
     * @var LearningProgressRepository
     */
    protected $learningProgressRepository;

    public function __construct(LearningProgressRepository $learningProgressRepository)
    {
        $this->learningProgressRepository = $learningProgressRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->learningProgressRepository
                ->aLearningProgressBelongsToParticipant($participant->getId(), $payload->getId());
    }

}
