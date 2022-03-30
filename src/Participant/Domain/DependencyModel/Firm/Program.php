<?php

namespace Participant\Domain\DependencyModel\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\ParticipantTypes;

class Program
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
     * @var int|null
     */
    protected $price;

    /**
     *
     * @var ArrayCollection
     */
    protected $registrationPhases;

    protected function __construct()
    {
        
    }

    public function firmIdEquals(string $firmId): bool
    {
        return $this->firmId === $firmId;
    }

    public function isRegistrationOpenFor(string $participantType): bool
    {
        $p = function (Program\RegistrationPhase $registrationPhase) {
            return $registrationPhase->isOpen();
        };
        return !$this->removed 
                && !empty($this->registrationPhases->filter($p)->count()) 
                && $this->participantTypes->hasType($participantType);
    }

}
