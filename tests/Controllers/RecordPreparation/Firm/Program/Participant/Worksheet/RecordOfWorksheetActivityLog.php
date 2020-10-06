<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfWorksheet,
    Record,
    Shared\RecordOfActivityLog
};

class RecordOfWorksheetActivityLog implements Record
{
    /**
     *
     * @var RecordOfWorksheet
     */
    public $worksheet;
    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    public $id;
    
    public function __construct(RecordOfWorksheet $worksheet, RecordOfActivityLog $activityLog)
    {
        $this->worksheet = $worksheet;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Worksheet_id" => $this->worksheet->id,
            "ActivityLog_id" => $this->activityLog->id,
            "id" => $this->id,
        ];
    }

}
