<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Shared\Form\StringField;

class StringFieldSearchFilterData
{

    /**
     * 
     * @var StringField
     */
    protected $stringField;

    /**
     * 
     * @var int|null
     */
    protected $comparisonType;

    public function getStringField(): StringField
    {
        return $this->stringField;
    }

    public function getComparisonType(): ?int
    {
        return $this->comparisonType;
    }

    public function __construct(StringField $stringField, ?int $comparisonType)
    {
        $this->stringField = $stringField;
        $this->comparisonType = $comparisonType;
    }

}
