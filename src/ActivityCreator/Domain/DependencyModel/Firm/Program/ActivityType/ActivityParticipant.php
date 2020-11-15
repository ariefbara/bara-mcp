<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program\ActivityType;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity,
    service\ActivityDataProvider
};
use SharedContext\Domain\ValueObject\ {
    ActivityParticipantPriviledge,
    ActivityParticipantType
};

class ActivityParticipant
{

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityParticipantType
     */
    protected $participantType;

    /**
     *
     * @var ActivityParticipantPriviledge
     */
    protected $participantPriviledge;

    protected function __construct()
    {
        
    }

    public function canInitiateAndTypeEquals(ActivityParticipantType $activityParticipantType): bool
    {
        return $this->participantPriviledge->canInitiate() && $this->participantType->sameValueAs($activityParticipantType);
    }
    
    public function addInviteesToActivity(Activity $activity, ActivityDataProvider $activityDataProvider): void
    {
        if (!$this->participantPriviledge->canAttend()) {
            return;
        }
        
        if ($this->participantType->isCoordinatorType()) {
            foreach ($activityDataProvider->iterateInvitedCoordinatorList() as $coordinator) {
                $activity->addInvitee($coordinator, $this);
            }
        }elseif ($this->participantType->isConsultantType()) {
            foreach ($activityDataProvider->iterateInvitedConsultantList() as $consultant) {
                $activity->addInvitee($consultant, $this);
            }
        } elseif ($this->participantType->isManagerType()) {
            foreach ($activityDataProvider->iterateInvitedManagerList() as $manager) {
                $activity->addInvitee($manager, $this);
            }
        } elseif ($this->participantType->isParticipantType()) {
            foreach ($activityDataProvider->iterateInvitedParticipantList() as $participant) {
                $activity->addInvitee($participant, $this);
            }
        }
    }
    
    

}
