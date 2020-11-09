<?php

namespace Tests\Controllers\RecordPreparation\Firm\Manager;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfActivity,
    Firm\RecordOfManager,
    Record
};

class RecordOfManagerActivity implements Record
{

    /**
     *
     * @var RecordOfManager
     */
    public $manager;

    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    public $id;

    function __construct(RecordOfManager $manager, RecordOfActivity $activity)
    {
        $this->manager = $manager;
        $this->activity = $activity;
        $this->id = $activity->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Manager_id" => $this->manager->id,
            "Activity_id" => $this->activity->id,
            "id" => $this->id,
        ];
    }

}
