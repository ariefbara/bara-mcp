<?php

namespace Payment\Domain\Model\Firm;

use SharedContext\Domain\ValueObject\CustomerInfo;

class Team
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
     * @var Client
     */
    protected $creator;

    protected function __construct()
    {
        
    }
    
    public function generateCustomerInfo(): CustomerInfo
    {
        return new CustomerInfo($this->name, $this->creator->getEmail());
    }

}
