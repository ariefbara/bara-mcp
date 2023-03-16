<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\LearningProgressRepository;
use Query\Domain\Task\ViewListPayload;

class ViewLearningProgressList implements ParticipantQueryTask
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
     * @param ViewListPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->learningProgressRepository->learningProgressListBelongsToParticipant(
                $participant->getId(), $payload->getPage(), $payload->getPageSize());
    }

}
