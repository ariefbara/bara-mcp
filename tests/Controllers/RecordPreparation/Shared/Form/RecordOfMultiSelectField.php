<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\RecordOfForm
};

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
}
