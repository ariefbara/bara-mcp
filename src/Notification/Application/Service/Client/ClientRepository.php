<?php

namespace Notification\Application\Service\Client;

use Notification\Domain\Model\Firm\Client;

interface ClientRepository
{
    public function ofId(string $clientId): Client;
}
