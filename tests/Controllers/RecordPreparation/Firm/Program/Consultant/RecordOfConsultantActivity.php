<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfActivity,
    Firm\Program\RecordOfConsultant,
    Record
};

class RecordOfConsultantActivity implements Record
{

    /**
     *
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    public $id;

    function __construct(RecordOfConsultant $consultant, RecordOfActivity $activity)
    {
        $this->consultant = $consultant;
        $this->activity = $activity;
        $this->id = $activity->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Consultant_id" => $this->consultant->id,
            "Activity_id" => $this->activity->id,
            "id" => $this->id,
        ];
    }

}
