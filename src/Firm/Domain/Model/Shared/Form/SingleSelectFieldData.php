<?php

namespace Firm\Domain\Model\Shared\Form;

class SingleSelectFieldData
{

    /**
     *
     * @var SelectFieldData
     */
    protected $selectFieldData;
    protected $defaultValue;

    function getSelectFieldData(): SelectFieldData
    {
        return $this->selectFieldData;
    }

    function getDefaultValue()
    {
        return $this->defaultValue;
    }

    function __construct(SelectFieldData $selectFieldData, $defaultValue)
    {
        $this->selectFieldData = $selectFieldData;
        $this->defaultValue = $defaultValue;
    }

}
