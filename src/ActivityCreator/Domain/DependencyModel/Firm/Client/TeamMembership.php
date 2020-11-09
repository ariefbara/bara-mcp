<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Client;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Team\ProgramParticipation,
    Model\ParticipantActivity,
    service\ActivityDataProvider
};
use Resources\Exception\RegularException;

class TeamMembership
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
     * @var string
     */
    protected $teamId;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }

    public function initiateActivityInProgramParticipation(
            ProgramParticipation $programParticipation, string $activityId, ActivityType $activityType,
            ActivityDataProvider $activityDataProvider): ParticipantActivity
    {
        $this->assertActive();
        if (!$programParticipation->belongsToTeam($this->teamId)) {
            $errorDetail = "forbidden: can't make request on program participation of other team";
            throw RegularException::forbidden($errorDetail);
        }
        return $programParticipation->initiateActivity($activityId, $activityType, $activityDataProvider);
    }
    
    public function updateActivity(ParticipantActivity $participantActivity, ActivityDataProvider $activityDataProvider): void
    {
        $this->assertActive();
        
        if (!$participantActivity->belongsToTeam($this->teamId)) {
            $errorDetail = "forbidden: can't make request on activity not belongs to your team";
            throw RegularException::forbidden($errorDetail);
        }
        
        $participantActivity->update($activityDataProvider);
    }
    
    protected function assertActive()
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
