<?php

namespace SharedContext\Domain\ValueObject;

class CustomerInfo
{

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $email;

    public function __construct(string $name, string $email)
    {
        $this->name = $name;
        $this->email = $email;
    }
    
    public function toArray()
    {
        return [
            'given_name' => $this->name,
            'email' => $this->email,
        ];
    }

}
