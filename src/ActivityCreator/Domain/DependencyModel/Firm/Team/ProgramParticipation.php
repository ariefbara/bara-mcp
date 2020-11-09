<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Team;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\Participant,
    Model\ParticipantActivity,
    service\ActivityDataProvider
};

class ProgramParticipation
{

    /**
     *
     * @var string
     */
    protected $teamId;

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
    
    public function belongsToTeam(string $teamId): bool
    {
        return $this->teamId === $teamId;
    }

    public function initiateActivity(
            string $activityId, ActivityType $activityType, ActivityDataProvider $activityDataProvider): ParticipantActivity
    {
        return $this->participant->initiateActivity($activityId, $activityType, $activityDataProvider);
    }

}
