<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Personnel;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    Model\CoordinatorActivity
};
use Resources\Exception\RegularException;
use SharedContext\Domain\ValueObject\ActivityParticipantType;

class Coordinator
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
    
    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }
    
    public function initiateActivity(string $coordinatorActivityId, ActivityType $activityType, $activityDataProvider): CoordinatorActivity
    {
        $activityParticipantType = new ActivityParticipantType(ActivityParticipantType::COORDINATOR);
        if (!$activityType->canBeInitiatedBy($activityParticipantType)) {
            $errorDetail = "forbidden: coordinator not allowed to initiate this activity";
            throw RegularException::forbidden($errorDetail);
        }
        $activity = $this->program->createActivity($coordinatorActivityId, $activityType, $activityDataProvider);
        return new CoordinatorActivity($this, $coordinatorActivityId, $activity);
    }

}
