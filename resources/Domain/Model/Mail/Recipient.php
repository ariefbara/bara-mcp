<?php

namespace Resources\Domain\Model\Mail;

use Resources\{
    ValidationRule, ValidationService
};

class Recipient
{

    /**
     *
     * @var string
     */
    protected $address;

    /**
     *
     * @var string
     */
    protected $name;

    function getAddress(): string
    {
        return $this->address;
    }

    function getName(): string
    {
        return $this->name;
    }

    private function setAddress($address)
    {
        $errorDetail = "bad request: mail recipient address is required and must be in valid email address format";
        ValidationService::build()
            ->addRule(ValidationRule::email())
            ->execute($address, $errorDetail);
        $this->address = $address;
    }

    private function setName($name)
    {
        $errorDetail = "bad request: mail recipient name is required";
        ValidationService::build()
            ->addRule(ValidationRule::notEmpty())
            ->execute($name, $errorDetail);
        $this->name = $name;
    }

    public function __construct(string $address, string $name)
    {
        $this->setAddress($address);
        $this->setName($name);
    }

}
