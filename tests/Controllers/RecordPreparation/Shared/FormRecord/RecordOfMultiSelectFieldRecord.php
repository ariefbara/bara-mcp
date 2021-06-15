<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfMultiSelectField;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

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
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table("MultiSelectFieldRecord")->insert($this->toArrayForDbEntry());
    }

}
