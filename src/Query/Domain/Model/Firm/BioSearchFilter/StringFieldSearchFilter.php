<?php

namespace Query\Domain\Model\Firm\BioSearchFilter;

use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Shared\Form\StringField;
use SharedContext\Domain\ValueObject\TextFieldComparisonType;

class StringFieldSearchFilter
{

    /**
     * 
     * @var BioSearchFilter
     */
    protected $bioSearchFilter;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var StringField
     */
    protected $stringField;

    /**
     * 
     * @var TextFieldComparisonType
     */
    protected $comparisonType;

    public function getBioSearchFilter(): BioSearchFilter
    {
        return $this->bioSearchFilter;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getStringField(): StringField
    {
        return $this->stringField;
    }

    public function getComparisonTypeValue(): int
    {
        return $this->comparisonType->getValue();
    }

    protected function __construct()
    {
        
    }
    
    public function getComparisonTypeDisplayValue(): string
    {
        return $this->comparisonType->getDisplayValue();
    }
    
    public function stringFieldIdEquals(string $stringFieldId): bool
    {
        return $this->stringField->idEquals($stringFieldId);
    }
    
    public function buildSqlComparisonClause(string $value): string
    {
        return <<<_QUERY
StringFieldRecord.StringField_id = '{$this->stringField->getId()}' AND 
    StringFieldRecord.removed = false AND
    StringFieldRecord.value {$this->comparisonType->getComparisonQuery($value)}
_QUERY;
    }

}
