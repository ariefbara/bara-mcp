<?php

namespace User\Application\Service\User\ProgramParticipation\Worksheet;

class WorksheetCompositionId
{

    protected $userId, $programParticipationId, $worksheetId;

    function getUserId()
    {
        return $this->userId;
    }

    function getProgramParticipationId()
    {
        return $this->programParticipationId;
    }

    function getWorksheetId()
    {
        return $this->worksheetId;
    }

    function __construct($userId, $programParticipationId, $worksheetId)
    {
        $this->userId = $userId;
        $this->programParticipationId = $programParticipationId;
        $this->worksheetId = $worksheetId;
    }

}
