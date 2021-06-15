<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

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
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('StringFieldRecord')->insert($this->toArrayForDbEntry());
    }

}
