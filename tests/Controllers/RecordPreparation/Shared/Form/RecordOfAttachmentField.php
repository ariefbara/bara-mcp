<?php

namespace Tests\Controllers\RecordPreparation\Shared\Form;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\RecordOfForm
};

class RecordOfAttachmentField implements Record
{
    /**
     *
     * @var RecordOfForm
     */
    public $form;
    public $id, $name, $description, $position, $mandatory = false, 
            $minValue = null, $maxValue = null, $removed = false;
    
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
            "removed" => $this->removed,
        ];
    }
}
