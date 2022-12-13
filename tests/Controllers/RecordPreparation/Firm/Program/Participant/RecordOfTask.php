<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfTask implements Record
{

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;
    public $id;
    public $cancelled;
    public $name;
    public $description;
    public $dueDate;
    public $createdTime;
    public $modifiedTime;

    public function __construct(RecordOfParticipant $participant, $id)
    {
        $this->participant = $participant;
        $this->id = "task-$id-id";
        $this->cancelled = false;
        $this->name = "task $id name";
        $this->dueDate = (new DateTime('+1 weeks'))->format('Y-m-d');
        $this->description = "task $id description";
        $this->createdTime = (new DateTime('-2 weeks'))->format('Y-m-d H:i:s');
        $this->modifiedTime = (new DateTime('-1 weeks'))->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            'Participant_id' => $this->participant->id,
            'id' => $this->id,
            'cancelled' => $this->cancelled,
            'name' => $this->name,
            'description' => $this->description,
            'dueDate' => $this->dueDate,
            'createdTime' => $this->createdTime,
            'modifiedTime' => $this->modifiedTime,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Task')->insert($this->toArrayForDbEntry());
    }

}
