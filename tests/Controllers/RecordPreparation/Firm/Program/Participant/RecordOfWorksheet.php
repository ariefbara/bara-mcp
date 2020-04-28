<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Record,
    Shared\RecordOfFormRecord
};

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

    function __construct(RecordOfParticipant $participant, RecordOfFormRecord $formRecord, RecordOfMission $mission)
    {
        $this->participant = $participant;
        $this->formRecord = $formRecord;
        $this->mission = $mission;
        $this->id = $formRecord->id;
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

}
