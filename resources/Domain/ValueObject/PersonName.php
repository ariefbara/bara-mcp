<?php

namespace Resources\Domain\ValueObject;

use Resources\ {
    ValidationRule,
    ValidationService
};

class PersonName
{

    /**
     *
     * @var string
     */
    protected $firstName;

    /**
     *
     * @var string||null
     */
    protected $lastName;

    function getFirstName(): string
    {
        return $this->firstName;
    }

    function getLastName(): ?string
    {
        return $this->lastName;
    }

    protected function setFirstName(string $firstName): void
    {
        $errorDetail = 'bad request: first name is mandatory';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($firstName, $errorDetail);
        $this->firstName = $firstName;
    }

    function __construct(string $firstName, ?string $lastName)
    {
        $this->setFirstName($firstName);
        $this->lastName = $lastName;
    }
    
    public function getFullName(): string
    {
        return empty($this->lastName)? $this->firstName: $this->firstName . " " . $this->lastName;
    }

}
