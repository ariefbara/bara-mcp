<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Tests\Controllers\RecordPreparation\ {
    Record,
    Shared\Form\RecordOfStringField,
    Shared\RecordOfFormRecord
};

class RecordOfStringFieldRecord implements Record
{

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;

    /**
     *
     * @var RecordOfStringField
     */
    public $stringField;
    public $id, $value, $removed;

    function __construct(RecordOfFormRecord $formRecord, RecordOfStringField $stringField, $index)
    {
        $this->formRecord = $formRecord;
        $this->stringField = $stringField;
        $this->id = "stringFieldRecord-$index-id";
        $this->value = "string field record $index value";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "FormRecord_id" => $this->formRecord->id,
            "StringField_id" => $this->stringField->id,
            "id" => $this->id,
            "value" => $this->value,
            "removed" => $this->removed,
        ];
    }

}
