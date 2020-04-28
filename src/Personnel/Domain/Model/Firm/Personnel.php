<?php

namespace Personnel\Domain\Model\Firm;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm;
use Resources\ {
    Domain\ValueObject\Password,
    ValidationRule,
    ValidationService
};

class Personnel
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
     * @var string
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
    protected $removed;

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
        return $this->joinTime->format('Y-m-d H:i:s');
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function setName(string $name): void
    {
        $errorDetail = "bad request: personnel name is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    protected function __construct()
    {
        
    }

}
