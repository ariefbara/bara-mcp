<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\Form\RecordOfIntegerField,
    Shared\RecordOfFormRecord
};

class RecordOfIntegerFieldRecord implements Record
{

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;

    /**
     *
     * @var RecordOfIntegerField
     */
    public $integerField;
    public $id, $value, $removed;

    function __construct(RecordOfFormRecord $formRecord, RecordOfIntegerField $integerField, $index)
    {
        $this->formRecord = $formRecord;
        $this->integerField = $integerField;
        $this->id = "integerFieldRecord-$index-id";
        $this->value = 99;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "FormRecord_id" => $this->formRecord->id,
            "IntegerField_id" => $this->integerField->id,
            "id" => $this->id,
            "value" => $this->value,
            "removed" => $this->removed,
        ];
    }

}
