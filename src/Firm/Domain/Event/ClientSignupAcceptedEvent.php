<?php

namespace Firm\Domain\Event;

use Firm\Application\Listener\ClientActivationCodeGeneratedEventInterface;

class ClientSignupAcceptedEvent implements ClientActivationCodeGeneratedEventInterface
{
    const EVENT_NAME = "ClientSignupAcceptedEvent";

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

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getClientId(): string
    {
        return $this->clientId;
    }

    function __construct(string $firmId, string $clientId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
