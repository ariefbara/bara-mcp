<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfDedicatedMentor implements Record
{

    /**
     * 
     * @var RecordOfParticipant|null
     */
    public $participant;

    /**
     * 
     * @var RecordOfConsultant|null
     */
    public $consultant;
    public $id;
    public $modifiedTime;
    public $cancelled;

    public function __construct(?RecordOfParticipant $participant, ?RecordOfConsultant $consultant, $index)
    {
        $this->participant = $participant;
        $this->consultant = $consultant;
        $this->id = "dedicatedMentor-$index-id";
        $this->modifiedTime = (new DateTimeImmutable('-7 days'))->format('Y-m-d H:i:s');
        $this->cancelled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => isset($this->participant)? $this->participant->id : null,
            'Consultant_id' => isset($this->consultant)? $this->consultant->id : null,
            'id' => $this->id,
            'modifiedTime' => $this->modifiedTime,
            'cancelled' => $this->cancelled,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('DedicatedMentor')->insert($this->toArrayForDbEntry());
    }

}
 