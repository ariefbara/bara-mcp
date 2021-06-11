<?php

namespace Tests\Controllers\RecordPreparation\Firm\BioSearchFilter;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfIntegerField;

class RecordOfIntegerFieldSearchFilter implements Record
{
    /**
     * 
     * @var RecordOfBioSearchFilter
     */
    public $bioSearchFilter;
    /**
     * 
     * @var RecordOfIntegerField
     */
    public $integerField;
    public $id;
    public $disabled;
    public $comparisonType;
    
    public function __construct(RecordOfBioSearchFilter $bioSearchFilter, RecordOfIntegerField $integerField, $index)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->integerField = $integerField;
        $this->id = "integer-field-search-filter-$index-id";
        $this->disabled = false;
        $this->comparisonType = 1;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'BioSearchFilter_id' => $this->bioSearchFilter->firm->id,
            'IntegerField_id' => $this->integerField->id,
            'id' => $this->id,
            'disabled' => $this->disabled,
            'comparisonType' => $this->comparisonType,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('IntegerFieldSearchFilter')->insert($this->toArrayForDbEntry());
    }

}
