<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program;

use ActivityInvitee\Domain\DependencyModel\Firm\Team\ProgramParticipation;

class Participant
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $active;

    /**
     *
     * @var ProgramParticipation|null
     */
    protected $teamParticipant;

    protected function __construct()
    {
        
    }

    public function belongsToTeam(string $teamId): bool
    {
        return isset($this->teamParticipant) ? $this->teamParticipant->teamIdEquals($teamId) : false;
    }

}
