<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Record
};

class RecordOfRegistrationPhase implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id, $name, $startDate = null, $endDate = null, $removed = false;
    
    function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "registration-phase-$index-id";
        $this->name = "registration phase $index name";
        $this->startDate = (new DateTime('-7 days'))->format('Y-m-d');
        $this->endDate = (new DateTime('+7 days'))->format('Y-m-d');
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "name" => $this->name,
            "startDate" => $this->startDate,
            "endDate" => $this->endDate,
            "removed" => $this->removed,
        ];
    }

}
