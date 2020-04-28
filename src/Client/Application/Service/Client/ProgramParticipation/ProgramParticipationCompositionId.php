<?php

namespace Client\Application\Service\Client\ProgramParticipation;

class ProgramParticipationCompositionId
{

    protected $clientId, $programParticipationId;

    function getClientId()
    {
        return $this->clientId;
    }

    function getProgramParticipationId()
    {
        return $this->programParticipationId;
    }

    function __construct($clientId, $programParticipationId)
    {
        $this->clientId = $clientId;
        $this->programParticipationId = $programParticipationId;
    }

}
