<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class RecordOfMultiSelectField implements Record
{
    /**
     *
     * @var RecordOfForm
     */
    public $form;
    /**
     *
     * @var RecordOfSelectField
     */
    public $selectField;
    public $id, $minValue = null, $maxValue = null, $removed = false;
    
    function __construct(RecordOfForm $form, RecordOfSelectField $selectField)
    {
        $this->form = $form;
        $this->selectField = $selectField;
        $this->id = $selectField->id;
        $this->minValue = null;
        $this->maxValue = null;
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "SelectField_id" => $this->selectField->id,
            "minimumValue" => $this->minValue,
            "maximumValue" => $this->maxValue,
            "removed" => $this->removed,
        ];
    }
    
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->selectField->insert($connection);
        $connection->table('MultiSelectField')->insert($this->toArrayForDbEntry());
    }
}
