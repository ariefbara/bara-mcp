<?php

namespace Client\Domain\Model;

class ClientData
{

    /**
     *
     * @var string||null
     */
    protected $firstName;

    /**
     *
     * @var string||null
     */
    protected $lastName;

    /**
     *
     * @var string||null
     */
    protected $email;

    /**
     *
     * @var string||null
     */
    protected $password;

    function getFirstName(): ?string
    {
        return $this->firstName;
    }

    function getLastName(): ?string
    {
        return $this->lastName;
    }

    function getEmail(): ?string
    {
        return $this->email;
    }

    function getPassword(): ?string
    {
        return $this->password;
    }

    function __construct(?string $firstName, ?string $lastName, ?string $email, ?string $password)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
    }

}
