<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\Program;
use Client\Domain\DependencyModel\Firm\Program\Registrant;
use Client\Domain\Model\Client;

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

    public function __construct(Client $client, string $id, Registrant $registrant)
    {
        $this->client = $client;
        $this->id = $id;
        $this->registrant = $registrant;
    }

    public function isActiveRegistrationCorrespondWithProgram(Program $program): bool
    {
        return $this->registrant->isActiveRegistrationCorrespondWithProgram($program);
    }

}
