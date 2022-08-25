<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use SharedContext\Domain\ValueObject\ProgramSnapshot;

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

    public function getStatus(): string
    {
        return $this->registrant->getStatus();
    }

    public function getRegisteredTimeString(): string
    {
        return $this->registrant->getRegisteredTimeString();
    }

    public function getRegistrantInvoice(): ?RegistrantInvoice
    {
        return $this->registrant->getRegistrantInvoice();
    }
    
    public function getProgramSnapshot(): ProgramSnapshot
    {
        return $this->registrant->getProgramSnapshot();
    }

}
