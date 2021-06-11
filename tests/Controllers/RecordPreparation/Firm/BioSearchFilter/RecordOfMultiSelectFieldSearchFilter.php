<?php

namespace Tests\Controllers\RecordPreparation\Firm\BioSearchFilter;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfMultiSelectField;

class RecordOfMultiSelectFieldSearchFilter implements Record
{

    /**
     * 
     * @var RecordOfBioSearchFilter
     */
    public $bioSearchFilter;

    /**
     * 
     * @var RecordOfMultiSelectField
     */
    public $multiSelectField;
    public $id;
    public $disabled;
    public $comparisonType;

    public function __construct(RecordOfBioSearchFilter $bioSearchFilter, RecordOfMultiSelectField $multiSelectField,
            $index)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->multiSelectField = $multiSelectField;
        $this->id = "multiSelect-filter-$index-id";
        $this->disabled = false;
        $this->comparisonType = 1;
    }

    public function toArrayForDbEntry()
    {
        return [
            'BioSearchFilter_id' => $this->bioSearchFilter->firm->id,
            'MultiSelectField_id' => $this->multiSelectField->id,
            'id' => $this->id,
            'disabled' => $this->disabled,
            'comparisonType' => $this->comparisonType,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('MultiSelectFieldSearchFilter')->insert($this->toArrayForDbEntry());
    }

}
