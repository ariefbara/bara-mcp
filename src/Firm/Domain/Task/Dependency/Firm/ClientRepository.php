<?php

namespace Firm\Domain\Task\Dependency\Firm;

use Firm\Domain\Model\Firm\Client;

interface ClientRepository
{

    public function nextIdentity(): string;

    public function add(Client $client): void;

    public function ofId(string $id): Client;
}
