<?php

namespace Firm\Domain\Model\Firm;

class ClientRegistrationData
{

    /**
     * 
     * @var string|null
     */
    protected $firstName;

    /**
     * 
     * @var string|null
     */
    protected $lastName;

    /**
     * 
     * @var string|null
     */
    protected $email;

    /**
     * 
     * @var string|null
     */
    protected $password;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function __construct(?string $firstName, ?string $lastName, ?string $email, ?string $password)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
    }

}
