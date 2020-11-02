<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Client;

use ActivityCreator\Domain\DependencyModel\Firm\{
    Client,
    Program\Participant
};

class ClientProgramParticipation
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
     * @var Participant
     */
    protected $participant;

    protected function __construct()
    {
        
    }

}
