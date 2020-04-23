<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use Tests\Controllers\RecordPreparation\Record;

class RecordOfForm implements Record
{
    public $id, $name, $description;
    
    function __construct($index)
    {
        $this->id = "form-$index-id";
        $this->name = "form $index name";
        $this->description = "form $index description";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
        ];
    }

}
