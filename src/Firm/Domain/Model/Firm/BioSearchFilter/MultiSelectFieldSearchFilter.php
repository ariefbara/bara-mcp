<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\MultiSelectField;
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

    public function __construct(
            BioSearchFilter $bioSearchFilter, string $id,
            MultiSelectFieldSearchFilterData $multiSelectFieldSearchFilterData)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->id = $id;
        $this->disabled = false;
        $this->multiSelectField = $multiSelectFieldSearchFilterData->getMultiSelectField();
        $this->comparisonType = new SelectFieldComparisonType($multiSelectFieldSearchFilterData->getComparisonType());
    }

    public function update(BioSearchFilterData $bioSearchFilterData): void
    {
        $comparisonTypeValue = $bioSearchFilterData->pullComparisonTypeCorrespondWithMultiSelectField($this->multiSelectField);
        if (isset($comparisonTypeValue)) {
            $this->comparisonType = new SelectFieldComparisonType($comparisonTypeValue);
            $this->disabled = false;
        } else {
            $this->disabled = true;
        }
    }

}
