<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfConsultationFeedbackForm,
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
     * @var RecordOfConsultationFeedbackForm
     */
    public $participantConsultationFeedbackForm;
    /**
     *
     * @var RecordOfConsultationFeedbackForm
     */
    public $consultantConsultationFeedbackForm;
    public $id, $name, $sessionDuration = 60, $removed = false;
    
    function __construct(RecordOfProgram $program, ?RecordOfConsultationFeedbackForm $participantConsultationFeedbackForm,
        ?RecordOfConsultationFeedbackForm $consultantConsultationFeedbackForm, $index)
    {
        $this->program = $program;
        $this->participantConsultationFeedbackForm = $participantConsultationFeedbackForm;
        $this->consultantConsultationFeedbackForm = $consultantConsultationFeedbackForm;
        $this->id = "consultanting-$index-id";
        $this->name = "consultanting $index name";
        $this->sessionDuration = 60;
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "ConsultationFeedbackForm_idForParticipant" => (empty($this->participantConsultationFeedbackForm))? null: $this->participantConsultationFeedbackForm->id,
            "ConsultationFeedbackForm_idForConsultant" => (empty($this->consultantConsultationFeedbackForm))? null: $this->consultantConsultationFeedbackForm->id,
            "id" => $this->id,
            "name" => $this->name,
            "sessionDuration" => $this->sessionDuration,
            "removed" => $this->removed,
        ];
    }

}
