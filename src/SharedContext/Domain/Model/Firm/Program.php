<?php

namespace SharedContext\Domain\Model\Firm;

use Client\Domain\Model\ProgramInterface as InterfaceForClientBC;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\DependencyEntity\Firm\ProgramInterface as InterfaceForParticipantBC;
use Query\Domain\Model\Firm\ParticipantTypes;
use SharedContext\Domain\Model\Firm\Program\RegistrationPhase;
use User\Domain\Model\ProgramInterface as InterfaceForUserBC;

class Program implements InterfaceForClientBC, InterfaceForUserBC, InterfaceForParticipantBC
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
    protected $id;

    /**
     *
     * @var ParticipantTypes
     */
    protected $participantTypes;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    /**
     *
     * @var ArrayCollection
     */
    protected $registrationPhases;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function firmIdEquals(string $firmId): bool
    {
        return $this->firmId === $firmId;
    }

    public function isRegistrationOpenFor(string $participantType): bool
    {
        $p = function (RegistrationPhase $registrationPhase) {
            return $registrationPhase->isOpen();
        };
        return !$this->removed && $this->participantTypes->hasType($participantType) && (!empty($this->registrationPhases->filter($p)->count()));
    }

}
