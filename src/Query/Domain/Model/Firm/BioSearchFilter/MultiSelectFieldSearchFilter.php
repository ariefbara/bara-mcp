<?php

namespace Query\Domain\Model\Firm\BioSearchFilter;

use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Shared\Form\MultiSelectField;
use SharedContext\Domain\ValueObject\SelectFieldComparisonType;

class MultiSelectFieldSearchFilter
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
     * @var MultiSelectField
     */
    protected $multiSelectField;

    /**
     * 
     * @var SelectFieldComparisonType
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

    public function getMultiSelectField(): MultiSelectField
    {
        return $this->multiSelectField;
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
    
    public function multiSelectFieldIdEquals(string $multiSelectFieldId): bool
    {
        return $this->multiSelectField->idEquals($multiSelectFieldId);
    }
    
    public function buildSqlComparisonClause(array $value): string
    {
        return <<<_QUERY
MultiSelectFieldRecord.MultiSelectField_id = '{$this->multiSelectField->getId()}' AND 
    MultiSelectFieldRecord.removed = false AND 
    SelectedOption.removed = false AND
    SelectedOption.Option_id {$this->comparisonType->getComparisonQuery($value)}
_QUERY;
    }

}
