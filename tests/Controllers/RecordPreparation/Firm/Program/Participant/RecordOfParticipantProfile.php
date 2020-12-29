<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfParticipantProfile implements Record
{

    /**
     * 
     * @var RecordOfParticipant
     */
    public $participant;

    /**
     * 
     * @var RecordOfProgramsProfileForm
     */
    public $programsProfileForm;

    /**
     * 
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;
    public $removed;

    function __construct(
            RecordOfParticipant $participant, RecordOfProgramsProfileForm $programsProfileForm,
            RecordOfFormRecord $formRecord)
    {
        $this->participant = $participant;
        $this->programsProfileForm = $programsProfileForm;
        $this->formRecord = $formRecord;
        $this->id = $formRecord->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->participant->id,
            "ProgramsProfileForm_id" => $this->programsProfileForm->id,
            "FormRecord_id" => $this->formRecord->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
