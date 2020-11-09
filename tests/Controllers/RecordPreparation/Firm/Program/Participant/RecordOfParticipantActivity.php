<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfActivity,
    Firm\Program\RecordOfParticipant,
    Record
};

class RecordOfParticipantActivity implements Record
{

    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     *
     * @var RecordOfActivity
     */
    public $activity;
    public $id;

    function __construct(RecordOfParticipant $participant, RecordOfActivity $activity)
    {
        $this->participant = $participant;
        $this->activity = $activity;
        $this->id = $activity->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "Activity_id" => $this->activity->id,
            "id" => $this->id,
        ];
    }

}
