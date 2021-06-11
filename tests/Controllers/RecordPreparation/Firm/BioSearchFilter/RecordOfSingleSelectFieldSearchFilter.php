<?php

namespace Tests\Controllers\RecordPreparation\Firm\BioSearchFilter;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfSingleSelectField;

class RecordOfSingleSelectFieldSearchFilter implements Record
{

    /**
     * 
     * @var RecordOfBioSearchFilter
     */
    public $bioSearchFilter;

    /**
     * 
     * @var RecordOfSingleSelectField
     */
    public $singleSelectField;
    public $id;
    public $disabled;
    public $comparisonType;

    public function __construct(RecordOfBioSearchFilter $bioSearchFilter, RecordOfSingleSelectField $singleSelectField,
            $index)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->singleSelectField = $singleSelectField;
        $this->id = "single-select-filter-$index-id";
        $this->disabled = false;
        $this->comparisonType = 1;
    }

    public function toArrayForDbEntry()
    {
        return [
            'BioSearchFilter_id' => $this->bioSearchFilter->firm->id,
            'SingleSelectField_id' => $this->singleSelectField->id,
            'id' => $this->id,
            'disabled' => $this->disabled,
            'comparisonType' => $this->comparisonType,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('SingleSelectFieldSearchFilter')->insert($this->toArrayForDbEntry());
    }

}
