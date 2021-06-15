<?php

namespace Tests\Controllers\RecordPreparation\Shared\FormRecord\MultiSelectFieldRecord;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\SelectField\RecordOfOption;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfMultiSelectFieldRecord;

class RecordOfSelectedOption implements Record
{
    /**
     *
     * @var RecordOfMultiSelectFieldRecord
     */
    public $multiSelectFieldRecord;
    /**
     *
     * @var RecordOfOption
     */
    public $option;
    public $id, $removed;
    
    function __construct(RecordOfMultiSelectFieldRecord $multiSelectFieldRecord, RecordOfOption $option, $index)
    {
        $this->multiSelectFieldRecord = $multiSelectFieldRecord;
        $this->option = $option;
        $this->id = "selected-option-$index-id";
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "MultiSelectFieldRecord_id" => $this->multiSelectFieldRecord->id,
            "Option_id" => $this->option->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('SelectedOption')->insert($this->toArrayForDbEntry());
    }

}
