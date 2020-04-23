<?php

namespace Firm\Domain\Model\Firm;

class ProgramData
{

    protected $name, $description;

    function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
    }

    function getName()
    {
        return $this->name;
    }

    function getDescription()
    {
        return $this->description;
    }

}
