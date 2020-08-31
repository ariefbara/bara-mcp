<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;

interface ClientRepository
{

    public function ofEmail(string $firmIdentifier, string $email): Client;

    public function ofId(string $firmId, string $clientId): Client;

    public function update(): void;
}
