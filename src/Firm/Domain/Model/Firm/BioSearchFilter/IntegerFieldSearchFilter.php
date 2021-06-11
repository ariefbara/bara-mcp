<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\IntegerField;
use SharedContext\Domain\ValueObject\IntegerFieldComparisonType;

class IntegerFieldSearchFilter
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
     * @var IntegerField
     */
    protected $integerField;

    /**
     * 
     * @var IntegerFieldComparisonType
     */
    protected $comparisonType;

    public function __construct(
            BioSearchFilter $bioSearchFilter, string $id, IntegerFieldSearchFilterData $integerFieldSearchFilterData)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->id = $id;
        $this->disabled = false;
        $this->integerField = $integerFieldSearchFilterData->getIntegerField();
        $this->comparisonType = new IntegerFieldComparisonType($integerFieldSearchFilterData->getComparisonType());
    }

    public function update(BioSearchFilterData $bioSearchFilterData): void
    {
        $comparisonTypeValue = $bioSearchFilterData->pullComparisonTypeCorrespondWithIntegerField($this->integerField);
        if (isset($comparisonTypeValue)) {
            $this->comparisonType = new IntegerFieldComparisonType($comparisonTypeValue);
            $this->disabled = false;
        } else {
            $this->disabled = true;
        }
    }

}
