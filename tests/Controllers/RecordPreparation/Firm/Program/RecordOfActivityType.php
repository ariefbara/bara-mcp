<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Record
};

class RecordOfActivityType implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id;
    public $name;
    public $description;
    
    function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "activityType-$index-id";
        $this->name = "activity type $index name";
        $this->description = "activity type $index description";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
        ];
    }

}
