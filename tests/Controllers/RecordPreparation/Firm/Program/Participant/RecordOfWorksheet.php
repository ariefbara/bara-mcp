<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfWorksheet implements Record
{

    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;

    /**
     *
     * @var RecordOfMission
     */
    public $mission;
    public $id, $name, $removed = false;

    /**
     *
     * @var RecordOfWorksheet
     */
    public $parent;

    function __construct(RecordOfParticipant $participant, RecordOfFormRecord $formRecord, RecordOfMission $mission, $index)
    {
        $this->participant = $participant;
        $this->formRecord = $formRecord;
        $this->mission = $mission;
        $this->id = "worksheet-$index-id";
        $this->name = "worksheet {$this->id} name";
        $this->removed = false;
        $this->parent = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "FormRecord_id" => $this->formRecord->id,
            "Mission_id" => $this->mission->id,
            "id" => $this->id,
            "name" => $this->name,
            "removed" => $this->removed,
            "parent_id" => empty($this->parent)? null: $this->parent->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->formRecord->insert($connection);
        $connection->table('Worksheet')->insert($this->toArrayForDbEntry());
    }

}
