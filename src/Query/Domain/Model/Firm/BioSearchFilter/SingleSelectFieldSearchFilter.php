<?php

namespace Query\Domain\Model\Firm\BioSearchFilter;

use Query\Domain\Model\Firm\BioSearchFilter;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use SharedContext\Domain\ValueObject\SelectFieldComparisonType;

class SingleSelectFieldSearchFilter
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
     * @var SingleSelectField
     */
    protected $singleSelectField;

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

    public function getSingleSelectField(): SingleSelectField
    {
        return $this->singleSelectField;
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
    
    public function singleSelectFieldIdEquals(string $singleSelectFieldId): bool
    {
        return $this->singleSelectField->idEquals($singleSelectFieldId);
    }
    
    public function buildSqlComparisonClause(array $listOfOptionId): string
    {
        return <<<_QUERY
SingleSelectFieldRecord.SingleSelectField_id = '{$this->singleSelectField->getId()}' AND
    SingleSelectFieldRecord.removed = false AND
    SingleSelectFieldRecord.Option_id {$this->comparisonType->getComparisonQuery($listOfOptionId)}
_QUERY;
    }

}
