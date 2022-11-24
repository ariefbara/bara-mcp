<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Consultant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfConsultantTask implements Record
{

    /**
     * 
     * @var RecordOfConsultant
     */
    public $consultant;

    /**
     * 
     * @var RecordOfTask
     */
    public $task;
    public $id;

    public function __construct(RecordOfConsultant $consultant, RecordOfTask $task)
    {
        $this->consultant = $consultant;
        $this->task = $task;
        $this->id = $task->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Consultant_id' => $this->consultant->id,
            'Task_id' => $this->task->id,
            'id' => $this->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->task->insert($connection);
        $connection->table('ConsultantTask')->insert($this->toArrayForDbEntry());
    }

}
