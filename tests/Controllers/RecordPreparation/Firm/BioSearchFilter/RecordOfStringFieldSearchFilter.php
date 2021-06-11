<?php

namespace Tests\Controllers\RecordPreparation\Firm\BioSearchFilter;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;

class RecordOfStringFieldSearchFilter implements Record
{

    /**
     * 
     * @var RecordOfBioSearchFilter
     */
    public $bioSearchFilter;

    /**
     * 
     * @var RecordOfStringField
     */
    public $stringField;
    public $id;
    public $disabled;
    public $comparisonType;

    public function __construct(RecordOfBioSearchFilter $bioSearchFilter, RecordOfStringField $stringField, $index)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->stringField = $stringField;
        $this->id = "string-field-search-filter-$index-id";
        $this->disabled = false;
        $this->comparisonType = 1;
    }

    public function toArrayForDbEntry()
    {
        return [
            'BioSearchFilter_id' => $this->bioSearchFilter->firm->id,
            'StringField_id' => $this->stringField->id,
            'id' => $this->id,
            'disabled' => $this->disabled,
            'comparisonType' => $this->comparisonType,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('StringFieldSearchFilter')->insert($this->toArrayForDbEntry());
    }

}
