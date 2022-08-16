<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Team;
use Resources\Application\Event\ContainEvents;

class TeamParticipant implements ContainEvents
{

    /**
     * 
     * @var Team
     */
    protected $team;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Participant
     */
    protected $participant;

    public function __construct(Team $team, string $id, Participant $participant)
    {
        $this->team = $team;
        $this->id = $id;
        $this->participant = $participant;
    }

    public function pullRecordedEvents(): array
    {
        return $this->participant->pullRecordedEvents();
    }
    
    public function isActiveParticipantOrRegistrantOfProgram(Program $program): bool
    {
        return $this->participant->isActiveParticipantOrRegistrantOfProgram($program);
    }

}
