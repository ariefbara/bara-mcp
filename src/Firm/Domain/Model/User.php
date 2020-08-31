<?php

namespace Firm\Domain\Model;

use Resources\Domain\ {
    Model\Mail\Recipient,
    ValueObject\PersonName
};

class User
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getPersonName(): PersonName
    {
        return $this->personName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    protected function __construct()
    {
        ;
    }
    
    public function getMailRecipient(): Recipient
    {
        return new Recipient($this->email, $this->personName);
    }
    
    public function getName(): string
    {
        return $this->personName->getFullName();
    }

}
