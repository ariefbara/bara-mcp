<?php

namespace Query\Domain\Model\Firm\Team;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use Query\Domain\Model\Firm\Team;
use SharedContext\Domain\ValueObject\ProgramSnapshot;

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
    
    public function getProgramSnapshot(): ProgramSnapshot
    {
        return $this->programRegistration->getProgramSnapshot();
    }

    public function getStatus(): string
    {
        return $this->programRegistration->getStatus();
    }

    public function getRegisteredTimeString(): string
    {
        return $this->programRegistration->getRegisteredTimeString();
    }
    
    public function getRegistrantInvoice(): ?RegistrantInvoice
    {
        return $this->programRegistration->getRegistrantInvoice();
    }

}
