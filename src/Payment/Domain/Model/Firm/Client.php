<?php

namespace Payment\Domain\Model\Firm;

use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\CustomerInfo;

class Client
{

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

    public function getEmail(): string
    {
        return $this->email;
    }

    protected function __construct()
    {
        
    }

    public function generateCustomerInfo(): CustomerInfo
    {
        return new CustomerInfo($this->personName->getFullName(), $this->email);
    }

}
