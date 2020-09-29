<?php

namespace Query\Domain\Service\Firm;

use Query\Domain\Model\Firm\Client;

interface ClientRepository
{
    public function aClientHavingEmail(string $firmId, string $clientEmail): Client;
}
