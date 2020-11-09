<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Client;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\Participant,
    Model\ParticipantActivity
};

class ProgramParticipation
{

    /**
     *
     * @var Client
     */
    protected $client;

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
    
    public function initiateActivity(string $activityId, ActivityType $activityType, $activityDataProvider): ParticipantActivity
    {
        return $this->participant->initiateActivity($activityId, $activityType, $activityDataProvider);
    }

}
