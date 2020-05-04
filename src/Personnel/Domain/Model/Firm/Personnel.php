<?php

namespace Personnel\Domain\Model\Firm;

use DateTimeImmutable;
use Query\Domain\Model\Firm;
use Resources\{
    Domain\ValueObject\Password,
    Exception\RegularException,
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

    protected function setName(string $name): void
    {
        $errorDetail = 'bad request: personnel name is mandatory';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setPhone(?string $phone): void
    {
        $errorDetail = "bad request: personnel phone format is invalid";
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::phone()))
                ->execute($phone, $errorDetail);
        $this->phone = $phone;
        ;
    }

    protected function __construct()
    {
        
    }

    public function updateProfile(string $name, ?string $phone): void
    {
        $this->setName($name);
        $this->setPhone($phone);
    }

    public function changePassword(string $previousPassword, string $newPassword): void
    {
        if (!$this->password->match($previousPassword)) {
            $errorDetail = "forbidden: previous password not match";
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($newPassword);
    }

}
