<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;

class ClientRegistrant
{

    /**
     *
     * @var Registrant
     */
    protected $registrant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;
    
    protected function __construct()
    {
    }
    
    public function createParticipant(Program $program, string $participantId): Participant
    {
        return Participant::participantForClient($program, $participantId, $this->client);
    }
    
    public function clientEquals(Client $client): bool
    {
        return $this->client === $client;
    }

}
