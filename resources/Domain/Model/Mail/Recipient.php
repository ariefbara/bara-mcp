<?php

namespace Resources\Domain\Model\Mail;

use Resources\{
    Domain\ValueObject\PersonName,
    ValidationRule,
    ValidationService
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
     * @var PersonName
     */
    protected $name;

    function getAddress(): string
    {
        return $this->address;
    }

    function getName(): PersonName
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

    public function __construct(string $address, PersonName $name)
    {
        $this->setAddress($address);
        $this->name = $name;
    }
    
    public function getFirstName(): string
    {
        return $this->name->getFirstName();
    }
    
    public function getFullName(): string
    {
        return $this->name->getFullName();
    }

}
