<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Query\Domain\Model\Firm;
use Resources\Domain\ValueObject\{
    Password,
    PersonName
};

class Client
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var PersonName
     */
    protected $personName;

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
     * @var DateTimeImmutable
     */
    protected $signupTime;

    /**
     *
     * @var string
     */
    protected $activationCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $activationCodeExpiredTime = null;

    /**
     *
     * @var string
     */
    protected $resetPasswordCode = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $resetPasswordCodeExpiredTime = null;

    /**
     *
     * @var bool
     */
    protected $activated = false;

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSignupTime(): DateTimeImmutable
    {
        return $this->signupTime;
    }

    public function isActivated(): bool
    {
        return $this->activated;
    }

    protected function __construct()
    {
        ;
    }

    public function getFullName(): string
    {
        return $this->personName->getFullName();
    }

    public function getFirstName(): string
    {
        return $this->personName->getFirstName();
    }

    public function getLastName(): string
    {
        return $this->personName->getLastName();
    }

    public function passwordMatch(string $password): bool
    {
        return $this->password->match($password);
    }

}
