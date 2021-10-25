<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

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
    public $disabled;
    
    function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "activityType-$index-id";
        $this->name = "activity type $index name";
        $this->description = "activity type $index description";
        $this->disabled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "disabled" => $this->disabled,
        ];
    }
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("ActivityType")->insert($this->toArrayForDbEntry());
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('ActivityType')->insert($this->toArrayForDbEntry());
    }

}
