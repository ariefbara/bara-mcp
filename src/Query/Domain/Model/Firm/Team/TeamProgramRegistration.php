<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\Model\Firm\{
    Program,
    Program\Registrant,
    Team
};

class TeamProgramRegistration
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
     * @var Registrant
     */
    protected $programRegistration;

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
        ;
    }

    public function getProgram(): Program
    {
        return $this->programRegistration->getProgram();
    }

    public function isConcluded(): bool
    {
        return $this->programRegistration->isConcluded();
    }

    public function getRegisteredTimeString(): string
    {
        return $this->programRegistration->getRegisteredTimeString();
    }

    public function getNote(): ?string
    {
        return $this->programRegistration->getNote();
    }

}
