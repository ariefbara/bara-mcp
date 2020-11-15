<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Team\ProgramParticipation as TeamParticipant,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation,
    Model\ParticipantActivity,
    service\ActivityDataProvider
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Participant implements CanReceiveInvitation
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $active;
    
    /**
     *
     * @var TeamParticipant|null
     */
    protected $teamParticipant;

    protected function __construct()
    {
        
    }

    public function belongsToTeam(string $teamId): bool
    {
        return isset($this->teamParticipant)? $this->teamParticipant->belongsToTeam($teamId): false;
    }

    public function initiateActivity(
            string $activityId, ActivityType $activityType, ActivityDataProvider $activityDataProvider): ParticipantActivity
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active participant can make this- request";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$activityType->canBeInitiatedBy(new ActivityParticipantType(ActivityParticipantType::PARTICIPANT))) {
            $errorDetail = "forbidden: participant not allowed to initiate this activity";
            throw RegularException::forbidden($errorDetail);
        }
        $activity = $this->program->createActivity($activityId, $activityType, $activityDataProvider);
        return new ParticipantActivity($this, $activityId, $activity);
    }

    public function canInvolvedInProgram(Program $program): bool
    {
        return $this->program === $program;
    }

    public function registerAsInviteeRecipient(Invitee $invitee): void
    {
        $invitee->registerParticipantAsRecipient($this);
    }

}
