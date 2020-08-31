<?php

namespace Firm\Domain\Event\Firm\Program;

use Config\EventList;
use Firm\Application\Listener\Firm\Program\ClientRegistrationAcceptedEventInterface;

class ClientRegistrationAccepted implements ClientRegistrationAcceptedEventInterface
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
    protected $clientId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function __construct(string $firmId, string $programId, string $clientId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->clientId = $clientId;
    }

    public function getName(): string
    {
        return EventList::CLIENT_REGISTRATION_ACCEPTED;
    }

}
