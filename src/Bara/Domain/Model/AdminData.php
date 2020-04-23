<?php

namespace Bara\Domain\Model;

class AdminData
{
    protected $name, $email;
    
    function getName()
    {
        return $this->name;
    }

    function getEmail()
    {
        return $this->email;
    }

    function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

}
