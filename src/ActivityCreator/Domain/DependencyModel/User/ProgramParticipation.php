<?php

namespace ActivityCreator\Domain\DependencyModel\User;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\Participant,
    Model\ParticipantActivity
};

class ProgramParticipation
{

    /**
     *
     * @var string
     */
    protected $userId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        
    }
    
    public function initiateActivity(string $activityId, $activityType, $activityDataProvider): ParticipantActivity
    {
        return $this->participant->initiateActivity($activityId, $activityType, $activityDataProvider);
    }

}
