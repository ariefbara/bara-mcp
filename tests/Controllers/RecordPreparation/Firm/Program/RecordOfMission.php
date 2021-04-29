<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfProgram,
    Firm\RecordOfWorksheetForm,
    Record
};

class RecordOfMission implements Record
{

    /**
     *
     * @var RecordOfProgram
     */
    public $program;

    /**
     *
     * @var RecordOfMission
     */
    public $parent;

    /**
     *
     * @var RecordOfWorksheetForm
     */
    public $worksheetForm;
    public $id, $name, $description, $published = false, $position;

    function __construct(?RecordOfProgram $program, ?RecordOfWorksheetForm $worksheetForm, $index,
        ?RecordOfMission $parent)
    {
        $this->program = $program;
        $this->parent = $parent;
        $this->worksheetForm = $worksheetForm;
        $this->id = "mission-$index-id";
        $this->name = "mission $index name";
        $this->description = "mission $index description";
        $this->position = "mission $index position";
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => isset($this->program)? $this->program->id: null,
            "parent_id" => empty($this->parent)? null: $this->parent->id,
            "WorksheetForm_id" => isset($this->worksheetForm) ? $this->worksheetForm->id: null,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "position" => $this->position,
            "published" => $this->published,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection)
    {
        $connection->table('Mission')->insert($this->toArrayForDbEntry());
    }

}
