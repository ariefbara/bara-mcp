<?php

namespace Client\Domain\Task\Repository\Firm\Client;

use Client\Domain\Model\Client\ClientRegistrant;

interface ClientRegistrantRepository
{

    public function add(ClientRegistrant $clientRegistrant): void;
    
    public function ofId(string $id): ClientRegistrant;
}
