<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Record,
    RecordOfClient
};

class RecordOfParticipant implements Record
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
    public $id, $acceptedTime, $active = true, $note = null;
    
    function __construct(RecordOfProgram $program, RecordOfClient $client, $index)
    {
        $this->program = $program;
        $this->client = $client;
        $this->id = "participant-$index-id";
        $this->acceptedTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->active = true;
        $this->note = null;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "Client_id" => $this->client->id,
            "id" => $this->id,
            "acceptedTime" => $this->acceptedTime,
            "active" => $this->active,
            "note" => $this->note,
        ];
    }

}
