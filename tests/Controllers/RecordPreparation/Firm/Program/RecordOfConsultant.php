<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfPersonnel,
    Firm\RecordOfProgram,
    Record
};

class RecordOfConsultant implements Record
{
    /**
     *
     * @var RecordOfProgram
     */
    public $program;
    /**
     *
     * @var RecordOfPersonnel
     */
    public $personnel;
    public $id;
    public $active;
    
    function __construct(RecordOfProgram $program, RecordOfPersonnel $personnel, $index)
    {
        $this->program = $program;
        $this->personnel = $personnel;
        $this->id = "consultant-$index";
        $this->active = true;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "Personnel_id" => $this->personnel->id,
            "active" => $this->active,
        ];
    }

}
