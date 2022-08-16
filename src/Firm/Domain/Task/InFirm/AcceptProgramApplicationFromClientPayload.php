<?php

namespace Firm\Domain\Task\InFirm;

class AcceptProgramApplicationFromClientPayload
{

    protected $programId;
    protected $clientId;
    public $acceptedClientParticipantId;

    public function getProgramId()
    {
        return $this->programId;
    }

    public function getClientId()
    {
        return $this->clientId;
    }

    public function __construct($programId, $clientId)
    {
        $this->programId = $programId;
        $this->clientId = $clientId;
    }

}
