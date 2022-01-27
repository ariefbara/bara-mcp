<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfMetric implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    public $id;
    public $name;
    public $description;
    public $minValue;
    public $maxValue;
    public $higherIsBetter;
    
    public function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "metric-$index-id";
        $this->name = "metric $index name";
        $this->description = "metric $index description";
        $this->minValue = 1;
        $this->maxValue = 999999;
        $this->higherIsBetter = null;
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "minimumValue" => $this->minValue,
            "maximumValue" => $this->maxValue,
            "higherIsBetter" => $this->higherIsBetter,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Metric')->insert($this->toArrayForDbEntry());
    }

}
