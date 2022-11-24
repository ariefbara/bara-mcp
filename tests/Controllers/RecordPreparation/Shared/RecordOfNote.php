<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfNote implements Record
{

    public $id;
    public $name;
    public $description;
    public $createdTime;
    public $modifiedTime;
    public $removed = false;

    public function __construct($index)
    {
        $this->id = "note-$index-id";
        $this->name = "note $index name";
        $this->description = "note $index description";
        $this->createdTime = (new DateTime('-1 weeks'))->format('Y-m-d H:i:s');
        $this->modifiedTime = (new DateTime('-1 weeks'))->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
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
