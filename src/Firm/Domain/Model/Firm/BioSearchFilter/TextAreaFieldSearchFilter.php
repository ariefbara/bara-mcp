<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Firm\BioSearchFilter;
use Firm\Domain\Model\Firm\BioSearchFilterData;
use Firm\Domain\Model\Shared\Form\TextAreaField;
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

    public function __construct(
            BioSearchFilter $bioSearchFilter, string $id, TextAreaFieldSearchFilterData $textAreaFieldSearchFilterData)
    {
        $this->bioSearchFilter = $bioSearchFilter;
        $this->id = $id;
        $this->disabled = false;
        $this->textAreaField = $textAreaFieldSearchFilterData->getTextAreaField();
        $this->comparisonType = new TextFieldComparisonType($textAreaFieldSearchFilterData->getComparisonType());
    }

    public function update(BioSearchFilterData $bioSearchFilterData): void
    {
        $comparisonTypeValue = $bioSearchFilterData->pullComparisonTypeCorrespondWithTextAreaField($this->textAreaField);
        if (isset($comparisonTypeValue)) {
            $this->comparisonType = new TextFieldComparisonType($comparisonTypeValue);
            $this->disabled = false;
        } else {
            $this->disabled = true;
        }
    }

}
