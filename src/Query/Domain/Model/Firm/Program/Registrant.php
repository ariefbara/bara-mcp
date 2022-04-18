<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration;
use Query\Domain\Model\User\UserRegistrant;
use SharedContext\Domain\ValueObject\ProgramSnapshot;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class Registrant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     * 
     * @var ProgramSnapshot
     */
    protected $programSnapshot;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var RegistrationStatus
     */
    protected $status;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $registeredTime;

    /**
     *
     * @var ClientRegistrant|null
     */
    protected $clientRegistrant;

    /**
     *
     * @var UserRegistrant|null
     */
    protected $userRegistrant;

    /**
     *
     * @var TeamProgramRegistration|null
     */
    protected $teamRegistrant;

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProgramSnapshot(): ProgramSnapshot
    {
        return $this->programSnapshot;
    }

    public function getStatus(): string
    {
        return $this->status->getValueName();
    }

    public function getRegisteredTimeString(): string
    {
        return $this->registeredTime->format('Y-m-d H:i:s');
    }

    protected function __construct()
    {
        
    }

    public function getClientRegistrant(): ?ClientRegistrant
    {
        return $this->clientRegistrant;
    }

    public function getUserRegistrant(): ?UserRegistrant
    {
        return $this->userRegistrant;
    }

    public function getTeamRegistrant(): ?TeamProgramRegistration
    {
        return $this->teamRegistrant;
    }

}
