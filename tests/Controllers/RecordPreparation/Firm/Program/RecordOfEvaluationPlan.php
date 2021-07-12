<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfEvaluationPlan implements Record
{

    /**
     *
     * @var RecordOfProgram
     */
    public $program;

    /**
     *
     * @var RecordOfFeedbackForm
     */
    public $feedbackForm;
    public $id;
    public $name;
    public $interval;
    public $disabled;
    /**
     * 
     * @var RecordOfMisssion
     */
    public $mission;

    function __construct(RecordOfProgram $program, ?RecordOfFeedbackForm $feedbackForm, $index, ?RecordOfMission $mission = null)
    {
        $this->program = $program;
        $this->feedbackForm = $feedbackForm;
        $this->id = "evaluation_plan-$index-id";
        $this->name = "evaluation plan $index name";
        $this->interval = 99;
        $this->disabled = false;
        $this->mission = $mission;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "FeedbackForm_id" => empty($this->feedbackForm)? null: $this->feedbackForm->id,
            "id" => $this->id,
            "name" => $this->name,
            "days_interval" => $this->interval,
            "disabled" => $this->disabled,
            "Mission_id" => empty($this->mission)? null: $this->mission->id,
        ];
    }

}
