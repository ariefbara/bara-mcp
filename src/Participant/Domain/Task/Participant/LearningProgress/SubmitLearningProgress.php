<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;
use Participant\Domain\Task\Participant\ParticipantTask;

class SubmitLearningProgress implements ParticipantTask
{

    /**
     * 
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    public function __construct(LearningMaterialRepository $learningMaterialRepository)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param SubmitLearningProgressPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $learningMaterial = $this->learningMaterialRepository->ofId($payload->getLearningMaterialId());
        $learningMaterial->assertAccessibleByParticipant($participant);
        
        $participant->submitLearningProgress($learningMaterial, $payload->getLearningProgressData());
    }

}
