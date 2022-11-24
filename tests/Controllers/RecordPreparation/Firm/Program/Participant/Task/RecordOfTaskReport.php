<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfTaskReport implements Record
{

    /**
     * 
     * @var RecordOfTask
     */
    public $task;
    public $id;
    public $content;
    public $createdTime;
    public $modifiedTime;

    public function __construct(RecordOfTask $task, $id)
    {
        $this->task = $task;
        $this->id = "TaskReport-$id-id";
        $this->content = "task report $id content";
        $this->createdTime = (new DateTime('-2 weeks'))->format('Y-m-d H:i:s');
        $this->modifiedTime = (new DateTime('-1 weeks'))->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            'Task_id' => $this->task->id,
            'id' => $this->id,
            'content' => $this->content,
            'createdTime' => $this->createdTime,
            'modifiedTime' => $this->modifiedTime,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('TaskReport')->insert($this->toArrayForDbEntry());
    }

}
