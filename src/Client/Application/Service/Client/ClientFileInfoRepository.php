<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ClientFileInfo;

interface ClientFileInfoRepository
{

    public function add(ClientFileInfo $clientFileInfo): void;

    public function nextIdentity(): string;
}
