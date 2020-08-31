<?php

namespace Client\Domain\Event;

use Firm\Application\Listener\ClientResetPasswordCodeGeneratedEventInterface;

class ClientResetPasswordCodeGenerated implements ClientResetPasswordCodeGeneratedEventInterface
{

    const EVENT_NAME = "ClientResetPasswordCodeGenerated";

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
