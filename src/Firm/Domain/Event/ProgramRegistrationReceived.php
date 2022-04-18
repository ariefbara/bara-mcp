<?php

namespace Firm\Domain\Event;

use Config\EventList;
use Resources\Application\Event\Event;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class ProgramRegistrationReceived implements Event
{

    /**
     * 
     * @var string
     */
    protected $registrantId;

    /**
     * 
     * @var RegistrationStatus
     */
    protected $registrationStatus;

    public function getRegistrantId(): string
    {
        return $this->registrantId;
    }

    public function getRegistrationStatus(): RegistrationStatus
    {
        return $this->registrationStatus;
    }

    public function __construct(string $registrantId, RegistrationStatus $registrationStatus)
    {
        $this->registrantId = $registrantId;
        $this->registrationStatus = $registrationStatus;
    }

    public function getName(): string
    {
        return EventList::PROGRAM_REGISTRATION_RECEIVED;
    }

}
