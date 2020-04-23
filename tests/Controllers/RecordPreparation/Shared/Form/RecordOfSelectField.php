<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Tests\Controllers\RecordPreparation\Record;

class RecordOfSelectField implements Record
{
    public $id, $name, $description, $position, $mandatory = false;
    
    function __construct($index)
    {
        $this->id = "select-field-$index-id";
        $this->name = "select field $index name";
        $this->description = "select field $index description";
        $this->position = "select field $index position";
        $this->mandatory = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "position" => $this->position,
            "mandatory" => $this->mandatory,
        ];
    }

}
