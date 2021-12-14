<?php

namespace Query\Domain\Model\Firm\Team\Member;

use Query\Domain\Model\Firm\Client;

class InspectedClientList
{

    /**
     * 
     * @var Client2[]
     */
    protected $clientList;

    public function __construct()
    {
        $this->clientList = [];
    }

    public function addClient(Client $client): void
    {
        $this->clientList[] = $client;
    }

    public function isInspectingClient(Client $client): bool
    {
        return empty($this->clientList) ? true : in_array($client, $this->clientList);
    }

}
