<?php

namespace Firm\Domain\Model\Firm\Program;

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
     * @var string
     */
    protected $clientId;
    
    protected function __construct()
    {
        ;
    }
    
    public function createParticipant(Program $program, string $participantId): Participant
    {
        return Participant::participantForClient($program, $participantId, $this->clientId);
    }
    
    public function clientIdEquals(string $clientId): bool
    {
        return $this->clientId === $clientId;
    }

}
