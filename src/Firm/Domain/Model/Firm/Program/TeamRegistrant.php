<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;

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
     * @var string
     */
    protected $teamId;
    
    protected function __construct()
    {
    }
    
    public function teamIdEquals(string $teamId): bool
    {
        return $this->teamId === $teamId;
    }
    
    public function createParticipant(Program $program, string $participantId): Participant
    {
        return Participant::participantForTeam($program, $participantId, $this->teamId);
    }
}
