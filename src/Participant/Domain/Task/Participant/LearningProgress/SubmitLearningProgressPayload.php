<?php

namespace Participant\Domain\Task\Participant\LearningProgress;

use Participant\Domain\Model\Participant\LearningProgressData;

class SubmitLearningProgressPayload
{

    /**
     * 
     * @var string
     */
    protected $learningMaterialId;

    /**
     * 
     * @var LearningProgressData
     */
    protected $learningProgressData;

    public function getLearningMaterialId(): string
    {
        return $this->learningMaterialId;
    }

    public function getLearningProgressData(): LearningProgressData
    {
        return $this->learningProgressData;
    }

    public function setLearningMaterialId(string $learningMaterialId)
    {
        $this->learningMaterialId = $learningMaterialId;
        return $this;
    }

    public function setLearningProgressData(LearningProgressData $learningProgressData)
    {
        $this->learningProgressData = $learningProgressData;
        return $this;
    }

}
