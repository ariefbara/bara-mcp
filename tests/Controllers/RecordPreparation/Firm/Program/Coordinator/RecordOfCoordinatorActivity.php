<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfActivity,
    Firm\Program\RecordOfCoordinator,
    Record
};

class RecordOfCoordinatorActivity implements Record
{

    /**
     *
     * @var RecordOfCoordinator
     */
    public $coordinator;

    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    public $id;

    function __construct(RecordOfCoordinator $coordinator, RecordOfActivity $activity)
    {
        $this->coordinator = $coordinator;
        $this->activity = $activity;
        $this->id = $activity->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Coordinator_id" => $this->coordinator->id,
            "Activity_id" => $this->activity->id,
            "id" => $this->id,
        ];
    }

}
