<?php

namespace Firm\Domain\Task\InFirm;

class AddLearningMaterialPayload
{

    /**
     * 
     * @var string
     */
    protected $missionId;

    /**
     * 
     * @var LearningMaterialRequest
     */
    protected $learningMaterialRequest;

    public function getMissionId(): string
    {
        return $this->missionId;
    }

    public function getLearningMaterialRequest(): LearningMaterialRequest
    {
        return $this->learningMaterialRequest;
    }

    public function __construct(string $missionId, LearningMaterialRequest $learningMaterialRequest)
    {
        $this->missionId = $missionId;
        $this->learningMaterialRequest = $learningMaterialRequest;
    }

}
