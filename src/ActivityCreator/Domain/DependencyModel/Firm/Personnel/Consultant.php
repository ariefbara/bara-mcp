<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Personnel;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation,
    Model\ConsultantActivity
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Consultant implements CanReceiveInvitation
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }
    
    public function initiateActivity(string $activityId, ActivityType $activityType, $activityDataProvider): ConsultantActivity
    {
        if (!$activityType->canBeInitiatedBy(new ActivityParticipantType(ActivityParticipantType::CONSULTANT))) {
            $errorDetail = "forbidden: consultant not allowed to initiate this activity";
            throw RegularException::forbidden($errorDetail);
        }
        $activity = $this->program->createActivity($activityId, $activityType, $activityDataProvider);
        return new ConsultantActivity($this, $activityId, $activity);
    }

    public function canInvolvedInProgram(Program $program): bool
    {
        return $this->program === $program;
    }

    public function registerAsInviteeRecipient(Invitee $invitee): void
    {
        $invitee->registerConsultantAsRecipient($this);
    }

}
