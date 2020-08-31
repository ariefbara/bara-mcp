<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientFileInfo;

interface ClientFileInfoRepository
{

    public function ofId(string $firmId, string $clientId, string $clientFileInfoId): ClientFileInfo;
}
