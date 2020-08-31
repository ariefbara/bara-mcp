<?php

namespace Client\Domain\Event;

use Firm\Application\Listener\ClientActivationCodeGeneratedEventInterface;

class ClientActivationCodeGenerated implements ClientActivationCodeGeneratedEventInterface
{

    const EVENT_NAME = "ClientActivationCodeGenerated";

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

    public function __construct(string $firmId, string $clientId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

}
