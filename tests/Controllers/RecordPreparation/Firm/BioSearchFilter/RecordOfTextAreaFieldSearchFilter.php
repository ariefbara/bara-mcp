<?php

namespace Tests\Controllers\RecordPreparation\Firm\BioSearchFilter;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\RecordOfBioSearchFilter;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfTextAreaField;

class RecordOfTextAreaFieldSearchFilter implements Record
{
    /**
     * 
     * @var RecordOfBioSearchFilter
     */
    public $bioSearchFilter;
    /**
     * 
     * @var RecordOfTextAreaField
     */
    public $textAreaField;
    public $id;
    public $disabled;
    public $comparisonType;
    
    public function __construct(RecordOfBioSearchFilter $bioSearchFilter, RecordOfTextAreaField $textAreaField, $index)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->textAreaField = $textAreaField;
        $this->id = "textArea-field-search-filter-$index-id";
        $this->disabled = false;
        $this->comparisonType = 1;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'BioSearchFilter_id' => $this->bioSearchFilter->firm->id,
            'TextAreaField_id' => $this->textAreaField->id,
            'id' => $this->id,
            'disabled' => $this->disabled,
            'comparisonType' => $this->comparisonType,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('TextAreaFieldSearchFilter')->insert($this->toArrayForDbEntry());
    }

}
