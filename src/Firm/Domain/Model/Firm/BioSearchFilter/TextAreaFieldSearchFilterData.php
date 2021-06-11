<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Shared\Form\TextAreaField;

class TextAreaFieldSearchFilterData
{

    /**
     * 
     * @var TextAreaField
     */
    protected $textAreaField;

    /**
     * 
     * @var int|null
     */
    protected $comparisonType;

    public function getTextAreaField(): TextAreaField
    {
        return $this->textAreaField;
    }

    public function getComparisonType(): ?int
    {
        return $this->comparisonType;
    }

    public function __construct(TextAreaField $textAreaField, ?int $comparisonType)
    {
        $this->textAreaField = $textAreaField;
        $this->comparisonType = $comparisonType;
    }

}
