<?php

namespace Firm\Domain\Model\Firm\Program;

class ClientParticipant
{

    /**
     *
     * @var Participant
     */
    protected $participant;

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
    
    public function __construct(Participant $participant, string $id, string $clientId)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->clientId = $clientId;
    }
    
    public function correspondWithRegistrant(Registrant $registrant): bool
    {
        return $registrant->correspondWithClient($this->clientId);
    }

}
