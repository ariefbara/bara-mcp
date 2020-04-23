<?php

namespace Firm\Domain\Model\Firm;

class PersonnelData
{

    protected $name, $email, $password, $phone;

    function getName()
    {
        return $this->name;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getPassword()
    {
        return $this->password;
    }

    function getPhone()
    {
        return $this->phone;
    }

    function __construct($name, $email, $password, $phone)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
    }

}
