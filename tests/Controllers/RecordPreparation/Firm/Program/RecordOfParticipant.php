<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use SharedContext\Domain\ValueObject\ParticipantStatus;
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
    public $id;
    public $status;
    public $programPrice;

    function __construct(?RecordOfProgram $program, $index)
    {
        $firm = new RecordOfFirm($index);
        $this->program = isset($program)? $program: new RecordOfProgram($firm, $index);
        $this->id = "participant-$index-id";
        $this->status = ParticipantStatus::REGISTERED;
        $this->programPrice = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "status" => $this->status,
            "programPrice" => $this->programPrice,
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
