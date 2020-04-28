<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Record,
    RecordOfClient
};

class RecordOfRegistrant implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    /**
     *
     * @var RecordOfClient
     */
    public $client;
    public $id, $appliedTime, $concluded = false, $note = null;
    
    function __construct(RecordOfProgram $program, RecordOfClient $client, $index)
    {
        $this->program = $program;
        $this->client = $client;
        $this->id = "applicant-$index-id";
        $this->appliedTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->concluded = false;
        $this->note = null;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "Client_id" => $this->client->id,
            "id" => $this->id,
            "appliedTime" => $this->appliedTime,
            "concluded" => $this->concluded,
            "note" => $this->note,
        ];
    }

}
