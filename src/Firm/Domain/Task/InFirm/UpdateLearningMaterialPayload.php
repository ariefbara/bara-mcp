<?php

namespace Firm\Domain\Task\InFirm;

class UpdateLearningMaterialPayload
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var LearningMaterialRequest
     */
    protected $learningMaterialRequest;

    public function getId(): string
    {
        return $this->id;
    }

    public function getLearningMaterialRequest(): LearningMaterialRequest
    {
        return $this->learningMaterialRequest;
    }

    public function __construct(string $id, LearningMaterialRequest $learningMaterialRequest)
    {
        $this->id = $id;
        $this->learningMaterialRequest = $learningMaterialRequest;
    }

}
