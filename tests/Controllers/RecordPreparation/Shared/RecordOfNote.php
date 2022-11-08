<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfNote implements Record
{

    public $id;
    public $content = 'note content';
    public $createdTime;
    public $modifiedTime;
    public $removed = false;

    public function __construct($index)
    {
        $this->id = "note-$index-id";
        $this->createdTime = (new DateTime('-1 weeks'))->format('Y-m-d H:i:s');
        $this->modifiedTime = (new DateTime('-1 weeks'))->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'createdTime' => $this->createdTime,
            'modifiedTime' => $this->modifiedTime,
            'removed' => $this->removed,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Note')->insert($this->toArrayForDbEntry());
    }

}
