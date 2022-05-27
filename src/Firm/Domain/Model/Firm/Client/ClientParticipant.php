<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program\Participant;

class ClientParticipant
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

    public function __construct(Client $client, string $id, Participant $participant)
    {
        $this->client = $client;
        $this->id = $id;
        $this->participant = $participant;
    }

}
