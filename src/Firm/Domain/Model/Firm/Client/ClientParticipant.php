<?php

namespace Firm\Domain\Model\Firm\Client;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\Application\Event\ContainEvents;

class ClientParticipant implements ContainEvents
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

    public function isActiveParticipantOrRegistrantOfProgram(Program $program): bool
    {
        return $this->participant->isActiveParticipantOrRegistrantOfProgram($program);
    }

    public function pullRecordedEvents(): array
    {
        return $this->participant->pullRecordedEvents();
    }

}
