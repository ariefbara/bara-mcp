<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Model\Firm;

interface ClientRepository
{

    public function allNonPaginatedActiveClientInFirm(Firm $firm, array $clientIdList);
}
