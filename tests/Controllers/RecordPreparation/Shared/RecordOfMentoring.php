<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use Tests\Controllers\RecordPreparation\Record;

class RecordOfMentoring implements Record
{
    public $id;
    
    public function __construct($index)
    {
        $this->id = "mentoring-$index-id";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table('Mentoring')->insert($this->toArrayForDbEntry());
    }

}
