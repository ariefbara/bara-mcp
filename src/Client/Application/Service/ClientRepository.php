<?php

namespace Client\Application\Service;

use Client\Domain\Model\Client;

interface ClientRepository
{

    public function nextIdentity(): string;

    public function add(Client $client): void;

    public function update(): void;

    public function ofId(string $clientId): Client;

    public function ofEmail(string $email): Client;

    public function containRecordWithEmail(string $email): bool;
}
