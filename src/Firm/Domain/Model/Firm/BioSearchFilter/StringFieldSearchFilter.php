<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\StringField;
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

    public function __construct(
            BioSearchFilter $bioSearchFilter, string $id, StringFieldSearchFilterData $stringFieldSearchFilterData)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->id = $id;
        $this->disabled = false;
        $this->stringField = $stringFieldSearchFilterData->getStringField();
        $this->comparisonType = new TextFieldComparisonType($stringFieldSearchFilterData->getComparisonType());
    }

    public function update(BioSearchFilterData $bioSearchFilterData): void
    {
        $comparisonTypeValue = $bioSearchFilterData->pullComparisonTypeCorrespondWithStringField($this->stringField);
        if (isset($comparisonTypeValue)) {
            $this->comparisonType = new TextFieldComparisonType($comparisonTypeValue);
            $this->disabled = false;
        } else {
            $this->disabled = true;
        }
    }

}
