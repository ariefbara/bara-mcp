<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\EvaluationPlan;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\RecordOfCoordinator,
    Firm\Program\RecordOfEvaluationPlan,
    Firm\Program\RecordOfParticipant,
    Record,
    Shared\RecordOfFormRecord
};

class RecordOfEvaluationReport implements Record
{

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

    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;

    function __construct(
            RecordOfEvaluationPlan $evaluationPlan, RecordOfCoordinator $coordinator, RecordOfParticipant $participant,
            RecordOfFormRecord $formRecord)
    {
        $this->evaluationPlan = $evaluationPlan;
        $this->coordinator = $coordinator;
        $this->participant = $participant;
        $this->formRecord = $formRecord;
        $this->id = $this->formRecord->id;
    }
    public function toArrayForDbEntry()
    {
        return [
            "EvaluationPlan_id" => $this->evaluationPlan->id,
            "Coordinator_id" => $this->coordinator->id,
            "Participant_id" => $this->participant->id,
            "FormRecord_id" => $this->formRecord->id,
            "id" => $this->id,
        ];
    }

}
