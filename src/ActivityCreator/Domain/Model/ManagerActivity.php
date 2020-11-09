<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    service\ActivityDataProvider
};
use Resources\Domain\Model\EntityContainEvents;

class ManagerActivity extends EntityContainEvents
{

    /**
     *
     * @var Manager
     */
    protected $manager;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Activity
     */
    protected $activity;
    
    function __construct(Manager $manager, string $id, Activity $activity)
    {
        $this->manager = $manager;
        $this->id = $id;
        $this->activity = $activity;
    }
    
    public function update(ActivityDataProvider $activityDataProvider): void
    {
        $this->activity->update($activityDataProvider);
    }


}
