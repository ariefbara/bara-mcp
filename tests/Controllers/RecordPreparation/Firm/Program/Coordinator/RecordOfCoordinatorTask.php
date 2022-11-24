<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfCoordinatorTask implements Record
{

    /**
     * 
     * @var RecordOfCoordinator
     */
    public $coordinator;

    /**
     * 
     * @var RecordOfTask
     */
    public $task;
    public $id;

    public function __construct(RecordOfCoordinator $coordinator, RecordOfTask $task)
    {
        $this->coordinator = $coordinator;
        $this->task = $task;
        $this->id = $task->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Coordinator_id' => $this->coordinator->id,
            'Task_id' => $this->task->id,
            'id' => $this->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->task->insert($connection);
        $connection->table('CoordinatorTask')->insert($this->toArrayForDbEntry());
    }

}
