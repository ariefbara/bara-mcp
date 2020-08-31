<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\ {
    Client,
    Program,
    Program\Registrant
};


class ClientRegistrant
{

    /**
     *
     * @var Client
     */
    protected $client;

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

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getId(): string
    {
        return $this->id;
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
