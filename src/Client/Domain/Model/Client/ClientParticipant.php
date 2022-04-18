<?php

namespace Client\Domain\Model\Client;

use Client\Domain\DependencyModel\Firm\Program;
use Client\Domain\DependencyModel\Firm\Program\Participant;
use Client\Domain\Model\Client;

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

    public function isActiveParticipationCorrespondWithProgram(Program $program): bool
    {
        return $this->participant->isActiveParticipationCorrespondWithProgram($program);
    }

}
