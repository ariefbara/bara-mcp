<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team;

use Illuminate\Database\Connection;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Record;

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
    
    public function __construct(?RecordOfTeam $team, ?RecordOfParticipant $participant, $index = 999)
    {
        $this->team = isset($team)? $team: new RecordOfTeam(null, null, $index);
        $program = new RecordOfProgram($this->team->firm, $index);
        $this->participant = isset($participant)? $participant: new RecordOfParticipant($program, $index);
        $this->id = $this->participant->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Team_id" => $this->team->id,
            "Participant_id" => $this->participant->id,
            "id" => $this->id,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("TeamParticipant")->insert($this->toArrayForDbEntry());
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table("TeamParticipant")->insert($this->toArrayForDbEntry());
    }

}
