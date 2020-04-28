<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use Tests\Controllers\RecordPreparation\Record;

class RecordOfFileInfo implements Record
{
    public $id, $folders, $name, $size;
    
    function __construct($index)
    {
        $this->id = "fileInfo-$index-id";
        $this->folders = null;
        $this->name = "file info $index name.txt";
        $this->size = null;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "folders" => $this->folders,
            "name" => $this->name,
            "size" => $this->size,
        ];
    }

}
