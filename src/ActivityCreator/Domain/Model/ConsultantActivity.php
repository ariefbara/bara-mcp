<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Personnel\Consultant,
    service\ActivityDataProvider
};
use Resources\Domain\Model\EntityContainEvents;

class ConsultantActivity extends EntityContainEvents
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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

    function __construct(Consultant $consultant, string $id, Activity $activity)
    {
        $this->consultant = $consultant;
        $this->id = $id;
        $this->activity = $activity;
    }
    
    public function update(ActivityDataProvider $activityDataProvider): void
    {
        $this->activity->update($activityDataProvider);
    }

}
