<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Activity;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfActivity,
    Record
};

class RecordOfInvitation implements Record
{
    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    public $id;
    public $removed;
    public $willAttend;
    public $attended;
    
    function __construct(RecordOfActivity $activity, $index)
    {
        $this->activity = $activity;
        $this->id = "invitation-$index-id";
        $this->removed = false;
        $this->willAttend = null;
        $this->attended = null;
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Activity_id" => $this->activity->id,
            "id" => $this->id,
            "removed" => $this->removed,
            "willAttend" => $this->willAttend,
            "attended" => $this->attended,
        ];
    }

}
