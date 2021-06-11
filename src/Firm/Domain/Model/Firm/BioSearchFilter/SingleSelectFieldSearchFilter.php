<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\SingleSelectField;
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

    public function __construct(
            BioSearchFilter $bioSearchFilter, string $id,
            SingleSelectFieldSearchFilterData $singleSelectFiledSearchFilterData)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->id = $id;
        $this->disabled = false;
        $this->singleSelectField = $singleSelectFiledSearchFilterData->getSingleSelectField();
        $this->comparisonType = new SelectFieldComparisonType($singleSelectFiledSearchFilterData->getComparisonType());
    }

    public function update(BioSearchFilterData $bioSearchFilterData): void
    {
        $comparisonTypeValue = $bioSearchFilterData->pullComparisonTypeCorrespondWithSingleSelectField($this->singleSelectField);
        if (isset($comparisonTypeValue)) {
            $this->comparisonType = new SelectFieldComparisonType($comparisonTypeValue);
            $this->disabled = false;
        } else {
            $this->disabled = true;
        }
    }

}
