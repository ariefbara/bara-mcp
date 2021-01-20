<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Client;

interface ClientRepository
{

    public function ofEmail(string $firmIdentifier, string $email): Client;

    public function ofId(string $firmId, string $clientId): Client;

    public function all(string $firmId, int $page, int $pageSize, ?bool $activatedStatus);
    
    public function aClientByEmail(string $firmId, string $email): Client;
}
