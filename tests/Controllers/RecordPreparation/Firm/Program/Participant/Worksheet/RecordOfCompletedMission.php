<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfCompletedMission implements Record
{
    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    /**
     *
     * @var RecordOfMission
     */
    public $mission;
    public $id;
    public $completedTime;
    
    public function __construct(RecordOfParticipant $participant, RecordOfMission $mission, $index)
    {
        $this->participant = $participant;
        $this->mission = $mission;
        $this->id = "completedMission-$index-id";
        $this->completedTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "Mission_id" => $this->mission->id,
            "id" => $this->id,
            "completedTime" => $this->completedTime,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('CompletedMission')->insert($this->toArrayForDbEntry());
    }

}
