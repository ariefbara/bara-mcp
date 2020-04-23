<?php

namespace Firm\Domain\Model\Shared\Form;

class IntegerFieldData
{

    /**
     *
     * @var FieldData
     */
    protected $fieldData;
    protected $minValue, $maxValue, $placeholder, $defaultValue;

    function getFieldData(): FieldData
    {
        return $this->fieldData;
    }

    function getMinValue()
    {
        return $this->minValue;
    }

    function getMaxValue()
    {
        return $this->maxValue;
    }

    function getPlaceholder()
    {
        return $this->placeholder;
    }

    function getDefaultValue()
    {
        return $this->defaultValue;
    }

    function __construct(FieldData $fieldData, $minValue, $maxValue, $placeholder, $defaultValue)
    {
        $this->fieldData = $fieldData;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->placeholder = $placeholder;
        $this->defaultValue = $defaultValue;
    }

}
