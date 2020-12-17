<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Illuminate\Database\Connection;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

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
    
    function __construct(?RecordOfProgram $program, ?RecordOfPersonnel $personnel, $index)
    {
        $this->program = isset($program)? $program: new RecordOfProgram(null, $index);
        $this->personnel = isset($personnel)? $personnel: new RecordOfPersonnel($this->program->firm, $index);
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
    
    public function persistSelf(Connection $connection): void
    {
        $connection->table("Consultant")->insert($this->toArrayForDbEntry());
    }
    
    public static function truncateTable(Connection $connection): void
    {
        $connection->table("Consultant")->truncate();
    }

}
