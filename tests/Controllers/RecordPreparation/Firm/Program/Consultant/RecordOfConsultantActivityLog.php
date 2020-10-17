<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfConsultant,
    Record,
    Shared\RecordOfActivityLog
};

class RecordOfConsultantActivityLog implements Record
{
    /**
     *
     * @var RecordOfConsultant
     */
    public $consultant;
    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    public $id;
    
    public function __construct(RecordOfConsultant $consultant, RecordOfActivityLog $activityLog)
    {
        $this->consultant = $consultant;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Consultant_id" => $this->consultant->id,
            "ActivityLog_id" => $this->activityLog->id,
            "id" => $this->id,
        ];
    }

}
