<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Client;

interface ClientRepository
{

    public function aClientInFirm(string $firmId, string $clientId): Client;
    
    public function executeNativeQuery(string $sqlQuery): array;
}
