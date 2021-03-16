<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\RecordOfObjective;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfObjectiveProgressReport implements Record
{

    /**
     * 
     * @var RecordOfObjective|null
     */
    public $objective;
    public $id;
    public $reportDate;
    public $submitTime;
    public $status;
    public $cancelled;

    public function __construct(?RecordOfObjective $objective, int $index)
    {
        $this->objective = $objective;
        $this->id = "objective-progress-report-$index-id";
        $this->reportDate = (new \DateTime("-$index days"))->format('Y-m-d');
        $this->submitTime = $submitTime = (new \DateTime())->format('Y-m-d H:i:s');
        $this->status = \SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus::UNCONCLUDED;
        $this->cancelled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Objective_id' => isset($this->objective)? $this->objective->id : null,
            'id' => $this->id,
            'reportDate' => $this->reportDate,
            'submitTime' => $this->submitTime,
            'status' => $this->status,
            'cancelled' => $this->cancelled,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table('ObjectiveProgressReport')->insert($this->toArrayForDbEntry());
    }

}
