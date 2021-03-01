<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfOKRPeriod;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfObjective implements Record
{
    /**
     * 
     * @var RecordOfOKRPeriod|null
     */
    public $okrPeriod;
    public $id;
    public $name;
    public $description;
    public $weight;
    public $disabled;
    
    public function __construct(?RecordOfOKRPeriod $okrPeriod, $index)
    {
        $this->okrPeriod = $okrPeriod;
        $this->id = "objective-$index-id";
        $this->name = "objective $index name";
        $this->description = "objective $index description";
        $this->weight = 30;
        $this->disabled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'OKRPeriod_id' => isset($this->okrPeriod) ? $this->okrPeriod->id : null,
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'weight' => $this->weight,
            'disabled' => $this->disabled,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Objective')->insert($this->toArrayForDbEntry());
    }

}
