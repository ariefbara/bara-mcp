<?php

namespace ActivityCreator\Domain\DependencyModel\Firm;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity
};
use Resources\Exception\RegularException;

class Program
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
    
    public function firmIdEquals(string $firmId): bool
    {
        return $this->firmId === $firmId;
    }

    public function createActivity(string $activityId, ActivityType $activityType, $activityDataProvider): Activity
    {
        if (!$activityType->belongsToProgram($this)) {
            $errorDetail = "forbidden: activity type belongs to different program";
            throw RegularException::forbidden($errorDetail);
        }
        return new Activity($this, $activityId, $activityType, $activityDataProvider);
    }

}
