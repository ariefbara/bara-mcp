<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\LearningProgressData;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\LearningProgressRepository;
use Participant\Domain\Task\Participant\ParticipantTask;

class UpdateLearningProgressMark implements ParticipantTask
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
     * @param LearningProgressData $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $learningProgress = $this->learningProgressRepository->ofId($payload->id);
        $learningProgress->assertManageableByParticipant($participant);
        
        $learningProgress->updateProgressMark($payload->getProgressMark());
    }

}
