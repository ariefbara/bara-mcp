<?php

namespace Query\Domain\Model\Firm\BioSearchFilter;

use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Shared\Form\TextAreaField;
use SharedContext\Domain\ValueObject\TextFieldComparisonType;

class TextAreaFieldSearchFilter
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
     * @var TextAreaField
     */
    protected $textAreaField;

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

    public function getTextAreaField(): TextAreaField
    {
        return $this->textAreaField;
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
    
    public function textAreaFieldIdEquals(string $textAreaFieldId): bool
    {
        return $this->textAreaField->idEquals($textAreaFieldId);
    }
    
    public function buildSqlComparisonClause(string $value): string
    {
        return <<<_QUERY
TextAreaFieldRecord.TextAreaField_id = '{$this->textAreaField->getId()}' AND 
    TextAreaFieldRecord.removed = false AND
    TextAreaFieldRecord.value {$this->comparisonType->getComparisonQuery($value)}
_QUERY;
    }

}
