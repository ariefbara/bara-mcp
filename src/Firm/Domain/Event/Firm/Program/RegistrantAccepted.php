<?php

namespace Firm\Domain\Event\Firm\Program;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\RegistrantAcceptedAsProgramParticipantEventInterface;

class RegistrantAccepted implements RegistrantAcceptedAsProgramParticipantEventInterface
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
    protected $programId;
    /**
     *
     * @var string
     */
    protected $participantId;
    
    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

        
    public function __construct(string $firmId, string $programId, string $participantId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->participantId = $participantId;
    }

    public function getName(): string
    {
        return EventList::REGISTRANT_ACCEPTED;
    }

}
