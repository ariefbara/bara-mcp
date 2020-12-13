<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Illuminate\Database\Connection;
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
    public $id, $name, $description, $participantTypes, $published = false, $removed = false;

    public function __construct(?RecordOfFirm $firm, $index)
    {
        $this->firm = isset($firm)? $firm: new RecordOfFirm($index);
        $this->id = "program-$index";
        $this->name = "program $index name";
        $this->description = "program $index description";
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
            "published" => $this->published,
            "participantTypes" => $this->participantTypes,
            "removed" => $this->removed,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Firm")->insert($this->toArrayForDbEntry());
    }

}
