<?php

namespace Tests\Controllers\RecordPreparation;

class RecordOfFirm implements Record
{
    public $id, $name, $identifier, $suspended = false;
    
    public function __construct($index, $identifier)
    {
        $this->id = "firm-$index-id";
        $this->name = "firm $index name";
        $this->identifier = $identifier;
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "identifier" => $this->identifier,
            "suspended" => $this->suspended,
        ];
    }

}
