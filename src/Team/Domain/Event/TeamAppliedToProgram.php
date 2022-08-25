<?php

namespace Team\Domain\Event;

use Resources\Application\Event\Event;

class TeamAppliedToProgram implements Event
{

    /**
     * 
     * @var string
     */
    protected $teamId;

    /**
     * 
     * @var string
     */
    protected $programId;

    public function getTeamId(): string
    {
        return $this->teamId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function __construct(string $teamId, string $programId)
    {
        $this->teamId = $teamId;
        $this->programId = $programId;
    }

    public function getName(): string
    {
        return \Config\EventList::TEAM_APPLIED_TO_PROGRAM;
    }

}
