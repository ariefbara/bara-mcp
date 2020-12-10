<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\Firm\RecordOfProfileForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfProgramsProfileForm implements Record
{
    /**
     * 
     * @var RecordOfProgram
     */
    public $program;
    /**
     * 
     * @var RecordOfProfileForm
     */
    public $profileForm;
    public $id;
    public $disabled;
    
    function __construct(RecordOfProgram $program, RecordOfProfileForm $profileForm, $index)
    {
        $this->program = $program;
        $this->profileForm = $profileForm;
        $this->id = "programsProfileForm-$index-id";
        $this->disabled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "ProfileForm_id" => $this->profileForm->id,
            "id" => $this->id,
            "disabled" => $this->disabled,
        ];
    }

}
