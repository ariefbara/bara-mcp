<?php

namespace Firm\Domain\Event;

use Client\Application\Listener\ParticipantNotificationEventInterface;
use Resources\Application\Event\Event;

class ParticipantAcceptedEvent implements Event, ParticipantNotificationEventInterface
{

    const EVENT_NAME = "ParticipantAcceptedEvent";

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

    /**
     *
     * @var string
     */
    protected $messageForClient;

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getProgramId(): string
    {
        return $this->programId;
    }

    function getParticipantId(): string
    {
        return $this->participantId;
    }

    function getMessageForClient(): string
    {
        return $this->messageForClient;
    }

    function __construct(string $firmId, string $programId, string $participantId, string $messageForClient)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->participantId = $participantId;
        $this->messageForClient = $messageForClient;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
