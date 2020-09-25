<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\Model\Firm\ {
    Program,
    Program\Participant,
    Team
};

class TeamProgramParticipation
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
    protected $programParticipation;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }
    
    public function getProgram(): Program
    {
        return $this->participant->getProgram();
    }

    public function getEnrolledTimeString(): string
    {
        return $this->participant->getEnrolledTimeString();
    }

    public function isActive(): bool
    {
        return $this->participant->isActive();
    }

    public function getNote(): ?string
    {
        return $this->participant->getNote();
    }

}
