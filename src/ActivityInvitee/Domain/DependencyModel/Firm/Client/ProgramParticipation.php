<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Client;

use ActivityInvitee\Domain\DependencyModel\Firm\ {
    Client,
    Program\Participant
};

class ProgramParticipation
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
