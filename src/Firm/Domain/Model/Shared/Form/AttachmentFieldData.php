<?php

namespace Firm\Domain\Model\Shared\Form;

class AttachmentFieldData
{

    /**
     *
     * @var FieldData
     */
    protected $fieldData;
    protected $minValue, $maxValue;

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

    function __construct(FieldData $fieldData, $minValue, $maxValue)
    {
        $this->fieldData = $fieldData;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

}
