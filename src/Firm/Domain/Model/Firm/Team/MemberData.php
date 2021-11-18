<?php

namespace Firm\Domain\Model\Firm\Team;

use Firm\Domain\Model\Firm\Client;

class MemberData
{

    /**
     * 
     * @var Client
     */
    protected $client;

    /**
     * 
     * @var string|null
     */
    protected $position;

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function __construct(Client $client, ?string $position)
    {
        $this->client = $client;
        $this->position = $position;
    }

}
