<?php

namespace Firm\Domain\Task\InFirm;

class AcceptProgramApplicationFromTeamPayload
{

    protected $teamId;
    protected $programId;
    public $acceptedTeamParticipantId;

    public function getTeamId()
    {
        return $this->teamId;
    }

    public function getProgramId()
    {
        return $this->programId;
    }

    public function __construct($teamId, $programId)
    {
        $this->teamId = $teamId;
        $this->programId = $programId;
    }

}
