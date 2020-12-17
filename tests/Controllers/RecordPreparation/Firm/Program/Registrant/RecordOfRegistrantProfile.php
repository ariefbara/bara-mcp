<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Registrant;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfProgramsProfileForm;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfRegistrantProfile implements Record
{

    /**
     * 
     * @var RecordOfRegistrant
     */
    public $registrant;

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
            RecordOfRegistrant $registrant, RecordOfProgramsProfileForm $programsProfileForm,
            RecordOfFormRecord $formRecord)
    {
        $this->registrant = $registrant;
        $this->programsProfileForm = $programsProfileForm;
        $this->formRecord = $formRecord;
        $this->id = $formRecord->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Registrant_id" => $this->registrant->id,
            "ProgramsProfileForm_id" => $this->programsProfileForm->id,
            "FormRecord_id" => $this->formRecord->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
