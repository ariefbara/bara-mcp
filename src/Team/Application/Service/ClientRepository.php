<?php

namespace Team\Application\Service;

use Team\Domain\DependencyModel\Firm\Client;

interface ClientRepository
{
    public function ofId(string $firmId, string $clientId): Client;
}
