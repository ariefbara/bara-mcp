<?php

namespace ActivityCreator\Domain\DependencyModel\Firm;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity\Invitee,
    Model\CanReceiveInvitation,
    Model\ManagerActivity
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Manager implements CanReceiveInvitation
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    protected function __construct()
    {
        
    }

    public function initiateActivityInProgram(
            string $activityId, Program $program, ActivityType $activityType, $activityDataProvider): ManagerActivity
    {
        if (!$program->firmIdEquals($this->firmId)) {
            $errorDetail = "forbidden: unable to create activity in program of other firm";
            throw RegularException::forbidden($errorDetail);
        }
        if (!$activityType->canBeInitiatedBy(new ActivityParticipantType(ActivityParticipantType::MANAGER))) {
            $errorDetail = "forbidden: this activity can't be initiated by manager role";
            throw RegularException::forbidden($errorDetail);
        }
        $activity = $program->createActivity($activityId, $activityType, $activityDataProvider);
        return new ManagerActivity($this, $activityId, $activity);
    }

    public function canInvolvedInProgram(Program $program): bool
    {
        return $program->firmIdEquals($this->firmId);
    }

    public function registerAsInviteeRecipient(Invitee $invitee): void
    {
        $invitee->registerManagerAsRecipient($this);
    }

}
