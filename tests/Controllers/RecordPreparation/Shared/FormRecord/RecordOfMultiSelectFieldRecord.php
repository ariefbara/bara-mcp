<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\Form\RecordOfMultiSelectField,
    Shared\RecordOfFormRecord
};

class RecordOfMultiSelectFieldRecord implements Record
{
    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    /**
     *
     * @var RecordOfMultiSelectField
     */
    public $multiSelectField;
    public $id, $removed;
    
    function __construct(RecordOfFormRecord $formRecord, RecordOfMultiSelectField $multiSelectField, $index)
    {
        $this->formRecord = $formRecord;
        $this->multiSelectField = $multiSelectField;
        $this->id = "multiSelectFieldRecord-$index-id";
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "FormRecord_id" => $this->formRecord->id,
            "MultiSelectField_id" => $this->multiSelectField->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
