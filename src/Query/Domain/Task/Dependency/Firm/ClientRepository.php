<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Client;

interface ClientRepository
{

    public function allNonPaginatedActiveClientInFirm(Firm $firm, array $clientIdList);

    public function allClientsInFirm(string $firmId, ClientFilter $filter);

    public function aClientInFirm(string $firmId, string $clientId): Client;
}
