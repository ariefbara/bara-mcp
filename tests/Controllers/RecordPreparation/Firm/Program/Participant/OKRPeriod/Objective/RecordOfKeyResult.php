<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\RecordOfObjective;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfKeyResult implements Record
{
    /**
     * 
     * @var RecordOfObjective|null
     */
    public $objective;
    public $id;
    public $name;
    public $description;
    public $target;
    public $weight;
    public $disabled;
    
    public function __construct(?RecordOfObjective $objective, $index)
    {
        $this->objective = $objective;
        $this->id = "key-result-$index-id";
        $this->name = "key result $index name";
        $this->description = "key result $index description";
        $this->target = 999999;
        $this->weight = 20;
        $this->disabled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Objective_id' => isset($this->objective) ? $this->objective->id : null,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'target' => $this->target,
            'weight' => $this->weight,
            'disabled' => $this->disabled,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table('KeyResult')->insert($this->toArrayForDbEntry());
    }

}
