<?php

namespace Firm\Domain\Model\Firm\BioSearchFilter;

use Firm\Domain\Model\Shared\Form\SingleSelectField;

class SingleSelectFieldSearchFilterData
{

    /**
     * 
     * @var SingleSelectField
     */
    protected $singleSelectField;

    /**
     * 
     * @var int|null
     */
    protected $comparisonType;

    public function getSingleSelectField(): SingleSelectField
    {
        return $this->singleSelectField;
    }

    public function getComparisonType(): ?int
    {
        return $this->comparisonType;
    }

    public function __construct(SingleSelectField $singleSelectField, ?int $comparisonType)
    {
        $this->singleSelectField = $singleSelectField;
        $this->comparisonType = $comparisonType;
    }

}
