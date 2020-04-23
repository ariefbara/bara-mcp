<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\RecordOfForm
};

class RecordOfTextAreaField implements Record
{
    /**
     *
     * @var RecordOfForm
     */
    public $form;
    public $id, $name, $description, $position, $mandatory = false, 
            $minValue = null, $maxValue = null, $placeholder, $defaultValue = null, $removed = false;
    
    function __construct(RecordOfForm $form, $index)
    {
        $this->form = $form;
        $this->id = "textarea-field-$index-id";
        $this->name = "textarea field $index name";
        $this->description= "textarea field $index description";
        $this->position = "textarea field $index position";
        $this->mandatory = false;
        $this->minValue = null;
        $this->maxValue = null;
        $this->placeholder = "textarea field $index placeholder";
        $this->defaultValue = "textarea field $index default value";
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Form_id" => $this->form->id,
            "id" => $this->id,
            "name" => $this->name,
            "description" => $this->description,
            "position" => $this->position,
            "mandatory" => $this->mandatory,
            "minimumValue" => $this->minValue,
            "maximumValue" => $this->maxValue,
            "placeholder" => $this->placeholder,
            "defaultValue" => $this->defaultValue,
            "removed" => $this->removed,
        ];
    }
}
