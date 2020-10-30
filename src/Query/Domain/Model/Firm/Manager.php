<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Query\Domain\Model\Firm;
use Resources\Domain\ValueObject\Password;

class Manager
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
     * @var string||null
     */
    protected $phone;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
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

    function getPhone(): ?string
    {
        return $this->phone;
    }

    function getJoinTimeString(): string
    {
        return $this->joinTime->format("Y-m-d H:i:s");
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
