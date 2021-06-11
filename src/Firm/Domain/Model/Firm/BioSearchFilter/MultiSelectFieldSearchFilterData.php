<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Shared\Form\MultiSelectField;

class MultiSelectFieldSearchFilterData
{

    /**
     * 
     * @var MultiSelectField
     */
    protected $multiSelectField;

    /**
     * 
     * @var int|null
     */
    protected $comparisonType;

    public function getMultiSelectField(): MultiSelectField
    {
        return $this->multiSelectField;
    }

    public function getComparisonType(): ?int
    {
        return $this->comparisonType;
    }

    public function __construct(MultiSelectField $multiSelectField, ?int $comparisonType)
    {
        $this->multiSelectField = $multiSelectField;
        $this->comparisonType = $comparisonType;
    }

}
