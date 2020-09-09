<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Client;

interface ClientRepository
{

    public function nextIdentity(): string;

    public function add(Client $client): void;

    public function containRecordWithEmail(string $firmIdentifier, string $clientEmail): bool;
    
    public function ofId(string $firmId, string $clientId): Client;
}
