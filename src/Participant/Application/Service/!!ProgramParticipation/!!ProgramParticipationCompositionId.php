<?php

namespace User\Application\Service\User\ProgramParticipation;

class ProgramParticipationCompositionId
{

    protected $userId, $programParticipationId;

    function getUserId()
    {
        return $this->userId;
    }

    function getProgramParticipationId()
    {
        return $this->programParticipationId;
    }

    function __construct($userId, $programParticipationId)
    {
        $this->userId = $userId;
        $this->programParticipationId = $programParticipationId;
    }

}
