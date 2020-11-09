<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\{
    DependencyModel\Firm\Personnel\Coordinator,
    service\ActivityDataProvider
};
use Resources\Domain\Model\EntityContainEvents;

class CoordinatorActivity extends EntityContainEvents
{

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

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

    function __construct(Coordinator $coordinator, string $id, Activity $activity)
    {
        $this->coordinator = $coordinator;
        $this->id = $id;
        $this->activity = $activity;
    }

    public function update(ActivityDataProvider $activityDataProvider): void
    {
        $this->activity->update($activityDataProvider);
    }

}
