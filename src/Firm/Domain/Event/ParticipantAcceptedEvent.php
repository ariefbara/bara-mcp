<?php

namespace Firm\Domain\Event;

use Resources\Application\Event\Event;

class ParticipantAcceptedEvent implements Event
{

    const EVENT_NAME = "ParticipantAcceptedEvent";

    /**
     *
     * @var string
     */
    protected $clientId;

    /**
     *
     * @var string
     */
    protected $participantId;

    /**
     *
     * @var string
     */
    protected $message;

    function getClientId(): string
    {
        return $this->clientId;
    }

    function getParticipantId(): string
    {
        return $this->participantId;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    function __construct(string $clientId, string $participantId, string $message)
    {
        $this->clientId = $clientId;
        $this->participantId = $participantId;
        $this->message = $message;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
