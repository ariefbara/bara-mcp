<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Client;

interface ClientRepository
{
    public function ofId(string $id): Client;
}
