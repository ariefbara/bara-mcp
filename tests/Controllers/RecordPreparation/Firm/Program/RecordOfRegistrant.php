<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfRegistrant implements Record
{

    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id, $registeredTime, $status;
    public $programName;
    public $programPrice;
    public $programAutoAccept;

    function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->programName = $this->program->name;
        $this->programPrice = $this->program->price;
        $this->programAutoAccept = $this->program->autoAccept;
        $this->id = "registrant-$index-id";
        $this->registeredTime = (new DateTime())->format('Y-m-d H:i:s');
        $this->status = RegistrationStatus::REGISTERED;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "program_name" => $this->programName,
            "program_price" => $this->programPrice,
            "program_autoAccept" => $this->programAutoAccept,
            "id" => $this->id,
            "registeredTime" => $this->registeredTime,
            "status" => $this->status,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Registrant')->insert($this->toArrayForDbEntry());
    }

}
