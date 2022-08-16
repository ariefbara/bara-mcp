<?php

namespace Firm\Domain\Task\InFirm;

class AddClientAsActiveProgramParticipantPayload
{

    protected $clientId;
    protected $programId;
    public $addedClientParticipantId;

    public function getClientId()
    {
        return $this->clientId;
    }

    public function getProgramId()
    {
        return $this->programId;
    }   

    public function __construct($clientId, $programId)
    {
        $this->clientId = $clientId;
        $this->programId = $programId;
    }

}
