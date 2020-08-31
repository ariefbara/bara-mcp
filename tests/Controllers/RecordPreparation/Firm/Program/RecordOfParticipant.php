<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Record
};

class RecordOfParticipant implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id, $enrolledTime, $active = true, $note = null;

    function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "participant-$index-id";
        $this->enrolledTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->active = true;
        $this->note = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "enrolledTime" => $this->enrolledTime,
            "active" => $this->active,
            "note" => $this->note,
        ];
    }

}
