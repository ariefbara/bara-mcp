<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\ {
    Model\Firm\Program\Mission\LearningMaterial,
    Model\Firm\Program\Participant,
    SharedModel\ActivityLog
};

class ViewLearningMaterialActivityLog
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var LearningMaterial
     */
    protected $learningMaterial;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLearningMaterial(): LearningMaterial
    {
        return $this->learningMaterial;
    }

    public function getActivityLog(): ActivityLog
    {
        return $this->activityLog;
    }

    protected function __construct()
    {
        ;
    }

}
