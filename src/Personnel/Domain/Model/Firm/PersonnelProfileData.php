<?php

namespace Personnel\Domain\Model\Firm;

class PersonnelProfileData
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function __construct(?string $firstName, ?string $lastName, ?string $phone, ?string $bio)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->bio = $bio;
    }

}
