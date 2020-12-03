<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\{
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfProgram,
    Record
};

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

    function __construct(RecordOfProgram $program, RecordOfFeedbackForm $feedbackForm, $index)
    {
        $this->program = $program;
        $this->feedbackForm = $feedbackForm;
        $this->id = "evaluation_plan-$index-id";
        $this->name = "evaluation plan $index name";
        $this->interval = 99;
        $this->disabled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "FeedbackForm_id" => $this->feedbackForm->id,
            "id" => $this->id,
            "name" => $this->name,
            "days_interval" => $this->interval,
            "disabled" => $this->disabled,
        ];
    }

}
