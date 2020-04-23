<?php

namespace Firm\Domain\Model\Shared\Form;

class FieldData
{

    protected $name, $description, $position, $mandatory;

    function getName()
    {
        return $this->name;
    }

    function getDescription()
    {
        return $this->description;
    }

    function getPosition()
    {
        return $this->position;
    }

    function getMandatory()
    {
        return $this->mandatory;
    }

    function __construct($name, $description, $position, $mandatory)
    {
        $this->name = $name;
        $this->description = $description;
        $this->position = $position;
        $this->mandatory = $mandatory;
    }

}
