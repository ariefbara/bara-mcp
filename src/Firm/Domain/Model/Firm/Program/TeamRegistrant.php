<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team;

class TeamRegistrant
{
    /**
     *
     * @var Registrant
     */
    protected $registrant;
    
    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Team
     */
    protected $team;
    
    protected function __construct()
    {
    }
    
    public function teamEquals(Team $team): bool
    {
        return $this->team === $team;
    }
    
    public function createParticipant(Program $program, string $participantId): Participant
    {
        return Participant::participantForTeam($program, $participantId, $this->team);
    }
}
