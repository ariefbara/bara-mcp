<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSingleSelectField;
use Tests\Controllers\RecordPreparation\Shared\Form\SelectField\RecordOfOption;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class RecordOfSingleSelectFieldRecord implements Record
{

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;

    /**
     *
     * @var RecordOfSingleSelectField
     */
    public $singleSelectField;

    /**
     *
     * @var RecordOfOption
     */
    public $option;
    public $id, $removed;

    function __construct(
            RecordOfFormRecord $formRecord, RecordOfSingleSelectField $singleSelectField, RecordOfOption $option, $index)
    {
        $this->formRecord = $formRecord;
        $this->singleSelectField = $singleSelectField;
        $this->option = $option;
        $this->id = "singleSelectFieldRecord-$index-id";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "FormRecord_id" => $this->formRecord->id,
            "SingleSelectField_id" => $this->singleSelectField->id,
            "id" => $this->id,
            "Option_id" => $this->option->id,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('SingleSelectFieldRecord')->insert($this->toArrayForDbEntry());
    }

}
