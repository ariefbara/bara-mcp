<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Resources\{
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

    protected function setEmail(string $email): void
    {
        $errorDetail = "bad request: personnel email is required in valid format";
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    protected function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    function __construct(Firm $firm, string $id, PersonnelData $personnelData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($personnelData->getName());
        $this->setEmail($personnelData->getEmail());
        $this->password = new Password($personnelData->getPassword());
        $this->setPhone($personnelData->getPhone());
        $this->joinTime = new DateTimeImmutable();
        $this->removed = false;
        $this->assignedAdmin = null;
    }

}
