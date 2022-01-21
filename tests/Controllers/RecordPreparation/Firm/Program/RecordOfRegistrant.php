<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfRegistrant implements Record
{

    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id, $registeredTime, $concluded = false, $note = null;

    function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "registrant-$index-id";
        $this->registeredTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->concluded = false;
        $this->note = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "registeredTime" => $this->registeredTime,
            "concluded" => $this->concluded,
            "note" => $this->note,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Registrant')->insert($this->toArrayForDbEntry());
    }

}
