<?php

namespace Query\Application\Service;

use Query\Domain\Model\Client;

interface ClientRepository
{

    public function ofEmail(string $email): Client;

    public function ofId(string $clientId): Client;

    public function all(int $page, int $pageSize);
}
