<?php

namespace Firm\Domain\Model\Firm;

class PersonnelData
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

    /**
     *
     * @var string|null
     */
    protected $phone;

    /**
     *
     * @var string|null
     */
    protected $bio;

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function __construct(
            ?string $firstName, ?string $lastName, ?string $email, ?string $password, ?string $phone, ?string $bio)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->bio = $bio;
    }

}
