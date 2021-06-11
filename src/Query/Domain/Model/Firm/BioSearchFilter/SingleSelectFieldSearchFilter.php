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

}
