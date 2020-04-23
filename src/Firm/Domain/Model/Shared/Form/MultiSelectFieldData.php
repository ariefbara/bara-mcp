<?php

namespace Firm\Domain\Model\Shared\Form;

class MultiSelectFieldData
{

    /**
     *
     * @var SelectFieldData
     */
    protected $selectFieldData;
    protected $minValue, $maxValue;

    function getSelectFieldData(): SelectFieldData
    {
        return $this->selectFieldData;
    }

    function getMinValue()
    {
        return $this->minValue;
    }

    function getMaxValue()
    {
        return $this->maxValue;
    }

    function __construct(SelectFieldData $selectFieldData, $minValue, $maxValue)
    {
        $this->selectFieldData = $selectFieldData;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

}
