<?php

namespace Query\Domain\Model\User;

use Query\Domain\Model\{
    Firm\Program,
    Firm\Program\Registrant,
    User
};

class UserRegistrant
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Registrant
     */
    protected $registrant;

    public function getId(): string
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    protected function __construct()
    {
        ;
    }

    public function getProgram(): Program
    {
        return $this->registrant->getProgram();
    }

    public function isConcluded(): bool
    {
        return $this->registrant->isConcluded();
    }

    public function getRegisteredTimeString(): string
    {
        return $this->registrant->getRegisteredTimeString();
    }

    public function getNote(): ?string
    {
        return $this->registrant->getNote();
    }

}
