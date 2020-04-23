<?php

namespace Firm\Domain\Model\Shared\Form\SelectField;

class OptionData
{

    protected $name, $description, $position;

    function __construct($name, $description, $position)
    {
        $this->name = $name;
        $this->description = $description;
        $this->position = $position;
    }

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

}
