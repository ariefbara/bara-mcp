<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class RecordOfProgram implements Record
{

    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    public $id, $name, $description, $participantTypes, $published = true, $removed = false;
    public $strictMissionOrder;

    public function __construct(?RecordOfFirm $firm, $index)
    {
        $this->firm = isset($firm)? $firm: new RecordOfFirm($index);
        $this->id = "program-$index";
        $this->name = "program $index name";
        $this->description = "program $index description";
        $this->strictMissionOrder = false;
        $this->participantTypes = ParticipantTypes::CLIENT_TYPE . "," . ParticipantTypes::USER_TYPE . "," . ParticipantTypes::TEAM_TYPE;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "strictMissionOrder" => $this->strictMissionOrder,
            "published" => $this->published,
            "participantTypes" => $this->participantTypes,
            "removed" => $this->removed,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Program")->insert($this->toArrayForDbEntry());
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Program')->insert($this->toArrayForDbEntry());
    }

}
