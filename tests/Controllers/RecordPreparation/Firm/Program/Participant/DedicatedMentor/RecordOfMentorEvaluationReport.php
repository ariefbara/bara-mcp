<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\DedicatedMentor;

use DateTimeImmutable;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfMentorEvaluationReport implements Record
{
    /**
     * 
     * @var RecordOfDedicatedMentor
     */
    public $dedicatedMentor;
    /**
     * 
     * @var RecordOfEvaluationPlan
     */
    public $evaluationPlan;
    /**
     * 
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $modifiedTime;
    public $cancelled = false;
    
    public function __construct(RecordOfDedicatedMentor $dedicatedMentor, RecordOfEvaluationPlan $evaluationPlan,
            RecordOfFormRecord $formRecord)
    {
        $this->dedicatedMentor = $dedicatedMentor;
        $this->evaluationPlan = $evaluationPlan;
        $this->formRecord = $formRecord;
        $this->modifiedTime = (new DateTimeImmutable('-1 weeks'))->format('Y-m-d H:i:s');
        $this->cancelled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'DedicatedMentor_id' => $this->dedicatedMentor->id,
            'EvaluationPlan_id' => $this->evaluationPlan->id,
            'FormRecord_id' => $this->formRecord->id,
            'id' => $this->formRecord->id,
            'modifiedTime' => $this->modifiedTime,
            'cancelled' => $this->cancelled,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->formRecord->insert($connection);
        $connection->table('MentorEvaluationReport')->insert($this->toArrayForDbEntry());
    }

}
