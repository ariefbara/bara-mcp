<?php

namespace Personnel\Domain\Model;

use DateTimeImmutable;
use Resources\Domain\ValueObject\Password;

class Client
{

    /**
     *
     * @var string
     */
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

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    protected function __construct()
    {
        ;
    }

}
