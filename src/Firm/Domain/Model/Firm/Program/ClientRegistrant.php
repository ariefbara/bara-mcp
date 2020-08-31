<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Client,
    Program
};

class ClientRegistrant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var Registrant
     */
    protected $registrant;

    public function getId(): string
    {
        return $this->id;
    }
    
    public function getClientId(): string
    {
        return $this->client->getId();
    }

    protected function __construct()
    {
        ;
    }

    public function accept(): void
    {
        $this->registrant->accept();
    }

    public function createParticipant(string $clientParticipantId): ClientParticipant
    {
        return new ClientParticipant($this->program, $clientParticipantId, $this->client);
    }

    public function reject(): void
    {
        $this->registrant->reject();
    }

    public function clientEquals(Client $client): bool
    {
        return $this->client === $client;
    }

}
