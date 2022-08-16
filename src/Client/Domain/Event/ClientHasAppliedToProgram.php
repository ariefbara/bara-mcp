<?php

namespace Client\Domain\Event;

use Resources\Application\Event\Event;

class ClientHasAppliedToProgram implements Event
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
    protected $clientId;

    /**
     * 
     * @var string
     */
    protected $programId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function __construct(string $firmId, string $clientId, string $programId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programId = $programId;
    }

    public function getName(): string
    {
        return \Config\EventList::CLIENT_HAS_APPLIED_TO_PROGRAM;
    }

}
