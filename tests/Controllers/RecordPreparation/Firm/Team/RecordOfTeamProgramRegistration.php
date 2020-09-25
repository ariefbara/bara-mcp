<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfRegistrant,
    Firm\RecordOfTeam,
    Record
};

class RecordOfTeamProgramRegistration implements Record
{
    /**
     *
     * @var RecordOfTeam
     */
    public $team;
    /**
     *
     * @var RecordOfRegistrant
     */
    public $registrant;
    public $id;
    
    public function __construct(RecordOfTeam $team, RecordOfRegistrant $registrant)
    {
        $this->team = $team;
        $this->registrant = $registrant;
        $this->id = $registrant->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Team_id" => $this->team->id,
            "Registrant_id" => $this->registrant->id,
            "id" => $this->id,
        ];
    }

}
