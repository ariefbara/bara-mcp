<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfTeam,
    Record
};

class RecordOfTeamProgramParticipation implements Record
{
    /**
     *
     * @var RecordOfTeam
     */
    public $team;
    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    public $id;
    
    public function __construct(RecordOfTeam $team, RecordOfParticipant $participant)
    {
        $this->team = $team;
        $this->participant = $participant;
        $this->id = $participant->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Team_id" => $this->team->id,
            "Participant_id" => $this->participant->id,
            "id" => $this->id,
        ];
    }

}
