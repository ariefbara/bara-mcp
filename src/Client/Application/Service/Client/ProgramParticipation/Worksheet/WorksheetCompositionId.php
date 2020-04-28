<?php

namespace Client\Application\Service\Client\ProgramParticipation\Worksheet;

class WorksheetCompositionId
{

    protected $clientId, $programParticipationId, $worksheetId;

    function getClientId()
    {
        return $this->clientId;
    }

    function getProgramParticipationId()
    {
        return $this->programParticipationId;
    }

    function getWorksheetId()
    {
        return $this->worksheetId;
    }

    function __construct($clientId, $programParticipationId, $worksheetId)
    {
        $this->clientId = $clientId;
        $this->programParticipationId = $programParticipationId;
        $this->worksheetId = $worksheetId;
    }

}
