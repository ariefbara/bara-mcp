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
    public $feedbackFormForm;
    public $id;
    public $name;
    public $interval;
    public $disabled;

    function __construct(RecordOfProgram $program, RecordOfFeedbackForm $feedbackFormForm, $index)
    {
        $this->program = $program;
        $this->feedbackFormForm = $feedbackFormForm;
        $this->id = "evaluation_plan-$index-id";
        $this->name = "evaluation plan $index name";
        $this->interval = 99;
        $this->disabled = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "FeedbackForm_id" => $this->feedbackFormForm->id,
            "id" => $this->id,
            "name" => $this->name,
            "days_interval" => $this->interval,
            "disabled" => $this->disabled,
        ];
    }

}
