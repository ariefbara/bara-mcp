<?php

namespace Query\Domain\Model;

use Resources\Domain\ValueObject\Password;

class Admin
{

    protected $id;

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var string
     */
    protected $email;

    /**
     * 
     * @var Password
     */
    protected $password;

    /**
     * 
     * @var bool
     */
    protected $removed = false;

    function getId()
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function passwordMatches(string $password): bool
    {
        return $this->password->match($password);
    }

}
