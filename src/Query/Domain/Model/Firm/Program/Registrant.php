<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\Model\ {
    Firm\Client\ClientRegistrant,
    Firm\Program,
    User\UserRegistrant
};

class Registrant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $registeredTime;

    /**
     *
     * @var string||null
     */
    protected $note;
    
    /**
     *
     * @var ClientRegistrant||null
     */
    protected $clientRegistrant;
    /**
     *
     * @var UserRegistrant||null
     */
    protected $userRegistrant;

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isConcluded(): bool
    {
        return $this->concluded;
    }

    public function getRegisteredTimeString(): string
    {
        return $this->registeredTime->format('Y-m-d H:i:s');
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    protected function __construct()
    {
        ;
    }

}
