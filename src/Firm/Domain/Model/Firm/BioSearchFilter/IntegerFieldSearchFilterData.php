<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Shared\Form\IntegerField;

class IntegerFieldSearchFilterData
{

    /**
     * 
     * @var IntegerField
     */
    protected $integerField;

    /**
     * 
     * @var int|null
     */
    protected $comparisonType;

    public function getIntegerField(): IntegerField
    {
        return $this->integerField;
    }

    public function getComparisonType(): ?int
    {
        return $this->comparisonType;
    }

    public function __construct(IntegerField $integerField, ?int $comparisonType)
    {
        $this->integerField = $integerField;
        $this->comparisonType = $comparisonType;
    }

}
