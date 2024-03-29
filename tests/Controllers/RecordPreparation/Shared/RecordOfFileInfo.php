<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use Illuminate\Database\ConnectionInterface;
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
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table("FileInfo")->insert($this->toArrayForDbEntry());
    }
    
    public function getFullyPath(): string
    {
        $path = '';
        $folders = [];
        if (!empty($this->folders)) {
            $folders = explode(',', $this->folders);
        }
        foreach ($folders as $folder) {
            $path .= DIRECTORY_SEPARATOR . $folder;
        }
        return $path . DIRECTORY_SEPARATOR . $this->name;
    }

}
