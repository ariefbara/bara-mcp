<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfTextAreaFieldRecord implements Record
{
    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    /**
     *
     * @var RecordOfTextAreaField
     */
    public $textAreaField;
    public $id, $value, $removed;
    
    function __construct(RecordOfFormRecord $formRecord, RecordOfTextAreaField $textAreaField, $index)
    {
        $this->formRecord = $formRecord;
        $this->textAreaField = $textAreaField;
        $this->id = "text-area-field-record-$index-id";
        $this->value = "text area field record $index value";
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "FormRecord_id" => $this->formRecord->id,
            "TextAreaField_id" => $this->textAreaField->id,
            "id" => $this->id,
            "value" => $this->value,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('TextAreaFieldRecord')->insert($this->toArrayForDbEntry());
    }

}
