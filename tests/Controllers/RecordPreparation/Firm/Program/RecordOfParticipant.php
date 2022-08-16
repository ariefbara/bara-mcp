<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class RecordOfParticipant implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id, $enrolledTime, $active = true, $note = null;

    function __construct(?RecordOfProgram $program, $index)
    {
        $firm = new RecordOfFirm($index);
        $this->program = isset($program)? $program: new RecordOfProgram($firm, $index);
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
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Participant")->insert($this->toArrayForDbEntry());
    }
    
    public function insert(ConnectionInterface $connection)
    {
        $connection->table('Participant')->insert($this->toArrayForDbEntry());
    }
}
