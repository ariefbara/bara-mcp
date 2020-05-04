<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfProgram,
    Record
};

class RecordOfConsultationSetup implements Record
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
    public $participantFeedbackForm;
    /**
     *
     * @var RecordOfFeedbackForm
     */
    public $consultantFeedbackForm;
    public $id, $name, $sessionDuration = 60, $removed = false;
    
    function __construct(RecordOfProgram $program, ?RecordOfFeedbackForm $participantFeedbackForm,
        ?RecordOfFeedbackForm $consultantFeedbackForm, $index)
    {
        $this->program = $program;
        $this->participantFeedbackForm = $participantFeedbackForm;
        $this->consultantFeedbackForm = $consultantFeedbackForm;
        $this->id = "consultanting-$index-id";
        $this->name = "consultanting $index name";
        $this->sessionDuration = 60;
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "FeedbackForm_idForParticipant" => (empty($this->participantFeedbackForm))? null: $this->participantFeedbackForm->id,
            "FeedbackForm_idForConsultant" => (empty($this->consultantFeedbackForm))? null: $this->consultantFeedbackForm->id,
            "id" => $this->id,
            "name" => $this->name,
            "sessionDuration" => $this->sessionDuration,
            "removed" => $this->removed,
        ];
    }

}
