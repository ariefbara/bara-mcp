<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\RecordOfForm
};

class RecordOfSingleSelectField implements Record
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
    public $id, $defaultValue = null, $removed = false;
    
    function __construct(RecordOfForm $form, RecordOfSelectField $selectField)
    {
        $this->form = $form;
        $this->selectField = $selectField;
        $this->id = $selectField->id;
        $this->defaultValue = null;
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "SelectField_id" => $this->selectField->id,
            "defaultValue" => $this->defaultValue,
            "removed" => $this->removed,
        ];
    }

}
