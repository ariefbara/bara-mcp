<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfEvaluation implements Record
{

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     * 
     * @var RecordOfEvaluationPlan
     */
    public $evaluationPlan;

    /**
     * 
     * @var RecordOfCoordinator
     */
    public $coordinator;
    public $id;
    public $submitTime;
    public $status;
    public $extendDays;

    function __construct(RecordOfParticipant $participant, RecordOfEvaluationPlan $evaluationPlan,
            RecordOfCoordinator $coordinator, $index)
    {
        $this->participant = $participant;
        $this->evaluationPlan = $evaluationPlan;
        $this->coordinator = $coordinator;
        $this->id = "evaluation-$index-id";
        $this->submitTime = (new \DateTimeImmutable("-1 weeks"))->format("Y-m-d H:i:s");
        $this->status = "pass";
        $this->extendDays = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
            "Coordinator_id" => $this->coordinator->id,
            "id" => $this->id,
            "submitTime" => $this->submitTime,
            "c_status" => $this->status,
            "extendDays" => $this->extendDays,
        ];
    }

}
