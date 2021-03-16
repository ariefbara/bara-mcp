<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfKeyResult;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfObjectiveProgressReport;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfKeyResultProgressReport implements Record
{
    /**
     * 
     * @var RecordOfObjectiveProgressReport|null
     */
    public $objectiveProgressReport;
    /**
     * 
     * @var RecordOfKeyResult|null
     */
    public $keyResult;
    public $id;
    public $value;
    public $disabled;
    
    public function __construct(?RecordOfObjectiveProgressReport $objectiveProgressReport,
            ?RecordOfKeyResult $keyResult, int $index)
    {
        $this->objectiveProgressReport = $objectiveProgressReport;
        $this->keyResult = $keyResult;
        $this->id = "key-result-progress-report-$index-id";
        $this->value = $index * 99;
        $this->disabled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'ObjectiveProgressReport_id' => isset($this->objectiveProgressReport) ? $this->objectiveProgressReport->id : null,
            'KeyResult_id' => isset($this->keyResult) ? $this->keyResult->id : null,
            'id' => $this->id,
            'value' => $this->value,
            'disabled' => $this->disabled,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('KeyResultProgressReport')->insert($this->toArrayForDbEntry());
    }

}
