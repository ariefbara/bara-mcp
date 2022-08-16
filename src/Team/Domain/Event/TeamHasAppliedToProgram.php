<?php

namespace Team\Domain\Event;

use Config\EventList;
use Resources\Application\Event\Event;

class TeamHasAppliedToProgram implements Event
{

    /**
     * 
     * @var string
     */
    protected $firmId;

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

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getTeamId(): string
    {
        return $this->teamId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function __construct(string $firmId, string $teamId, string $programId)
    {
        $this->firmId = $firmId;
        $this->teamId = $teamId;
        $this->programId = $programId;
    }

    public function getName(): string
    {
        return EventList::TEAM_HAS_APPLIED_TO_PROGRAM;
    }

}
