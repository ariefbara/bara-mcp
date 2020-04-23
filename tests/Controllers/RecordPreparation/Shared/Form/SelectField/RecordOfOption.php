<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form\SelectField;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\Form\RecordOfSelectField
};

class RecordOfOption implements Record
{

    /**
     *
     * @var RecordOfSelectField
     */
    public $selectField;
    public $id, $name, $description, $position, $removed = false;

    function __construct(RecordOfSelectField $selectField, $index)
    {
        $this->selectField = $selectField;
        $this->id = "option-$index-id";
        $this->name = "option $index name";
        $this->description = "option $index description";
        $this->position = "option $index position";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "SelectField_id" => $this->selectField->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "position" => $this->position,
            "removed" => $this->removed,
        ];
    }

}
